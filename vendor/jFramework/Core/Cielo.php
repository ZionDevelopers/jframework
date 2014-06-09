<?php
/**
 * jFramework
 *
 * @version 2.0.0
 * @link https://github.com/ZionDevelopers/jframework/ The jFramework GitHub Project
 * @copyright 2010-2014, Júlio César de Oliveira
 * @author Júlio César de Oliveira <talk@juliocesar.me>
 * @license http://www.apache.org/licenses/LICENSE-2.0.html Apache 2.0 License
 */

namespace jFramework\Core;

/**
 * Cielo (Brazilian Payment Gateway)
 *
 * Created: 2012-06-10 10:20 AM
 * Updated: 2014-06-03 09:47 AM
 * @version 1.2.0 
 * @package jFramework
 * @subpackage Core
 * @copyright Copyright (c) 2010-2014, Júlio César de Oliveira
 * @author Júlio César de Oliveira <talk@juliocesar.me>
 * @license http://www.apache.org/licenses/LICENSE-2.0.html Apache 2.0 License
 */
class Cielo
{
    /**
     *
     * @access private
     */
    private $log_file = null;
    private $xml = array();
    
    /**
     *     
     */
    public $dataEcNumber;
    public $dataEcKey;
    public $dataCarrierNumber;
    public $dataCarrierExp;
    public $dataCarrierInd;
    public $dataCarrierSecKey;
    public $dataCarrierName;
    public $dataOrderNumber;
    public $dataOrderPrice;
    public $dataOrderCurrency = "986";
    public $dataOrderDate;
    public $dataOrderDesc;
    public $dataOrderLanguage = "PT";
    public $paymentMethodFlag;
    public $paymentMethodProduct;
    public $paymentMethodParcels;
    public $returnURL;
    public $authorize;
    public $capture;
    public $tid;
    public $status;
    public $urlAuthorization;

    const ENCODING = "ISO-8859-1";
    const VERSION = "1.1.0";
    const ADDRESS_PRODUCTION = "https://ecommerce.cielo.com.br/servicos/ecommwsec.do";
    const ADDRESS_TEST = "https://qasecommerce.cielo.com.br/servicos/ecommwsec.do";

    /**
     * Construct
     *     
     * @param string $id
     */
    public function __construct($id)
    {
        $this->log_file = $this->folder(LOGS_DIR . "/cielo/" . date("Y/m/d/")) . date("H-i.") . $id . ".log";
    }

    /**
     * Build XML data
     * 
     * @access private
     */
    private function buildData()
    {
        // XML Strings
        $this->xml ['header'] = '<?xml version="1.0" encoding="' . self::ENCODING . '" ?>';
        $this->xml ['dataEc'] = '<dados-ec>' . "\n      " . '<numero>' . $this->dataEcNumber . '</numero>' . "\n      " . '<chave>' . $this->dataEcKey . '</chave>' . "\n   " . '</dados-ec>';
        $this->xml ['carrierData'] = '<dados-portador>' . "\n      " . '<numero>' . $this->dataCarrierNumber . '</numero>' . "\n      " . '<validade>' . $this->dataCarrierExp . '</validade>' . "\n      " . '<indicador>' . $this->dataCarrierInd . '</indicador>' . "\n      " . '<codigo-seguranca>' . $this->dataCarrierSecKey . '</codigo-seguranca>' . "\n   ";
        $this->dataOrderDate = date("Y-m-d") . "T" . date("H:i:s");

        // Verify if Carrier Name was Informed
        if (!empty($this->dataCarrierName)) {
            $this->xml ['carrierData'] .= '   <nome-portador>' . $this->dataCarrierName . '</nome-portador>' . "\n   ";
        }

        $this->xml ['carrierData'] .= '</dados-portador>';
        $this->xml ['cardData'] = str_replace('dados-portador', 'dados-cartao', $this->xml ['carrierData']);
        $this->xml ['orderData'] = '<dados-pedido>' . "\n      " . '<numero>' . $this->dataOrderNumber . '</numero>' . "\n      " . '<valor>' . $this->dataOrderPrice . '</valor>' . "\n      " . '<moeda>' . $this->dataOrderCurrency . '</moeda>' . "\n      " . '<data-hora>' . $this->dataOrderDate . '</data-hora>' . "\n      ";

        // Verify if Description was Informed
        if (!empty($this->dataOrderDesc)) {
            $this->xml ['orderData'] .= '<descricao>' . $this->dataOrderDesc . '</descricao>' . "\n      ";
        }

        $this->xml ['orderData'] .= '<idioma>' . $this->dataOrderLanguage . '</idioma>' . "\n   " . '</dados-pedido>';
        $this->xml ['paymentMethod'] = '<forma-pagamento>' . "\n      " . '<bandeira>' . $this->paymentMethodFlag . '</bandeira>' . "\n      " . '<produto>' . $this->paymentMethodProduct . '</produto>' . "\n      " . '<parcelas>' . $this->paymentMethodParcels . '</parcelas>' . "\n   " . '</forma-pagamento>';
        $this->xml ['returnURL'] = '<url-retorno>' . $this->returnURL . '</url-retorno>';
        $this->xml ['authorize'] = '<autorizar>' . $this->authorize . '</autorizar>';
        $this->xml ['capture'] = '<capturar>' . $this->capture . '</capturar>';
    }

    /**
     * Fix folder
     *     
     * @param string $path        	
     * @return string
     */
    public function folder($path)
    {
        if (!file_exists($path)) {
            if (!is_dir($path)) {
                mkdir($path, 0777, true);
            }
        }
        return $path;
    }

    /**
     * Send Request
     *     
     * @param string $post        	
     * @param string $transaction        	
     * @return SimpleXMLElement
     */
    public function send($post, $transaction)
    {
        // Write Log
        $this->logWrite("SEND: " . $post, $transaction);

        // Send Request to Cielo Website
        $vmResponse = $this->process(CIELO_PRODUCTION ? self::ADDRESS_PRODUCTION : self::ADDRESS_TEST, "mensagem=" . $post);
        $this->logWrite("RESPONSE: " . $vmResponse, $transaction);

        $this->verifyError($post, $vmResponse);

        return simplexml_load_string($vmResponse);
    }

    /**
     * Request Transaction
     *     
     * @param string $includeCarrier        	
     * @return SimpleXMLElement
     */
    public function requestTransaction($includeCarrier)
    {
        // Build XML Data
        $this->buildData();
        $data = $this->xml ['header'] . "\n" . '<requisicao-transacao id="' . md5(date("YmdHisu")) . '" versao="' . self::VERSION . '">' . "\n   " . $this->xml ['dataEc'] . "\n   ";
        if ($includeCarrier == true) {
            $data .= $this->xml ['dataCarrier'] . "\n   ";
        }
        $data .= $this->xml ['orderData'] . "\n   " . $this->xml ['paymentMethod'] . "\n   " . $this->xml ['returnURL'] . "\n   " . $this->xml ['authorize'] . "\n   " . $this->xml ['capture'] . "\n";
        $data .= '</requisicao-transacao>';

        return $this->send($data, "Transacao");
    }

    /**
     * Request TID
     *      
     * @return SimpleXMLElement
     */
    public function requestTid()
    {
        $data = $this->xml ['header'] . "\n" . '<requisicao-tid id="' . md5(date("YmdHisu")) . '" versao ="' . self::VERSION . '">' . "\n   " . $this->xml ['dataEc'] . "\n   " . $this->xml ['paymentMethod'] . "\n" . '</requisicao-tid>';
        return $this->send($data, "Requisicao Tid");
    }

    /**
     * Request Carrier Authorization
     *
     * @return SimpleXMLElement
     */
    public function requestCarrierAuthorization()
    {
        $data = $this->xml ['header'] . "\n" . '<requisicao-autorizacao-portador id="' . md5(date("YmdHisu")) . '" versao ="' . self::VERSION . '">' . "\n" . '<tid>' . $this->tid . '</tid>' . "\n   " . $this->xml ['dataEc'] . "\n   " . $this->xml ['dataCard'] . "\n   " . $this->xml ['dataOrder'] . "\n   " . $this->xml ['paymentMethod'] . "\n   " . '<capturar-automaticamente>' . $this->capture . '</capturar-automaticamente>' . "\n" . '</requisicao-autorizacao-portador>';
        return $this->send($data, "Autorizacao Portador");
    }

    /**
     * Request TID Authorization
     *
     * @return SimpleXMLElement
     */
    public function requestTidAuthorization()
    {
        $data = $this->xml ['header'] . "\n" . '<requisicao-autorizacao-tid id="' . md5(date("YmdHisu")) . '" versao="' . self::VERSION . '">' . "\n  " . '<tid>' . $this->tid . '</tid>' . "\n  " . $this->xml ['dataEc'] . "\n" . '</requisicao-autorizacao-tid>';
        return $this->send($data, "Autorizacao Tid");
    }

    /**
     * Request Capture
     *
     * @param string $capturePercent        	
     * @param string $attach        	
     * @return SimpleXMLElement
     */
    public function requestCapture($capturePercent, $attach)
    {
        $data = $this->xml ['header'] . "\n" . '<requisicao-captura id="' . md5(date("YmdHisu")) . '" versao="' . self::VERSION . '">' . "\n   " . '<tid>' . $this->tid . '</tid>' . "\n   " . $this->xml ['dataEc'] . "\n   " . '<valor>' . $capturePercent . '</valor>' . "\n";
        if (!empty($attach)) {
            $data .= '   <anexo>' . $attach . '</anexo>' . "\n";
        }
        $data .= '</requisicao-captura>';

        return $this->send($data, "Captura");
    }

    /**
     * Request Cancel
     *
     * @return SimpleXMLElement
     */
    public function requestCancel()
    {
        $data = $this->xml ['header'] . "\n" . '<requisicao-cancelamento id="' . md5(date("YmdHisu")) . '" versao="' . self::VERSION . '">' . "\n   " . '<tid>' . $this->tid . '</tid>' . "\n   " . $this->xml ['dataEc'] . "\n" . '</requisicao-cancelamento>';
        return $this->send($data, "Cancelamento");
    }

    /**
     * Request Inquiry
     *
     * @return SimpleXMLElement
     */
    public function requestInquiry()
    {
        $data = $this->xml ['header'] . "\n" . '<requisicao-consulta id="' . md5(date("YmdHisu")) . '" versao="' . self::VERSION . '">' . "\n   " . '<tid>' . $this->tid . '</tid>' . "\n   " . $this->xml ['dataEc'] . "\n" . '</requisicao-consulta>';
        return $this->send($data, "Consulta");
    }

    /**
     * Implode all XML data
     *
     * @return string
     */
    public function ToString()
    {
        return $this->xml ['header'] . '<objeto-pedido>' . '<tid>' . $this->tid . '</tid>' . '<status>' . $this->status . '</status>' . $this->xml ['dataEc'] . $this->xml ['orderData'] . $this->xml ['paymentMethod'] . '</objeto-pedido>';
    }

    /**
     * From String
     *
     * @param string $str
     */
    public function FromString($str)
    {
        $xml = simplexml_load_string($str);

        $this->tid = $xml->tid;
        $this->status = $xml->status;
        $this->dataEcKey = $xml->{'dados-ec'}->chave;
        $this->dataEcNumber = $xml->{'dados-ec'}->numero;
        $this->dataOrderNumber = $xml->{'dados-pedido'}->numero;
        $this->dataOrderDate = $xml->{'dados-pedido'}->{'data-hora'};
        $this->dataOrderPrice = $xml->{'dados-pedido'}->valor;
        $this->paymentMethodProduct = $xml->{'forma-pagamento'}->produto;
        $this->paymentMethodParcels = $xml->{'forma-pagamento'}->parcelas;
    }

    /**
     * Translate status code
     *
     * @return string
     */
    public function getStatus()
    {
        if ($this->status == 0) {
            $status = "Criada";
        } elseif ($this->status == 1) {
            $status = "Em andamento";
        } elseif ($this->status == 2) {
            $status = "Autenticada";
        } elseif ($this->status == 3) {
            $status = "Não autenticada";
        } elseif ($this->status == 4) {
            $status = "Autorizada";
        } elseif ($this->status == 5) {
            $status = "Não autorizada";
        } elseif ($this->status == 6) {
            $status = "Capturada";
        } elseif ($this->status == 8) {
            $status = "Não capturada";
        } elseif ($this->status == 9) {
            $status = "Cancelada";
        } elseif ($this->status == 10) {
            $status = "Em autenticação";
        } else {
            $status = "n/a";
        }

        return $status;
    }

    /**
     * Log Write
     * 
     * @param string $strMessage        	
     * @param string $transaction        	
     */
    public function logWrite($strMessage, $transaction)
    {
        $log = "***********************************************" . "\n";
        $log .= date("Y-m-d H:i:s:u (T)") . "\n";
        $log .= "FILE: " . $_SERVER ["REQUEST_URI"] . "\n";
        $log .= "TRANSACTION: " . $transaction . "\n";
        $log .= $strMessage . "\n\n";

        file_put_contents($this->log_file, $log, FILE_APPEND);
    }

    /**
     * Process Transaction
     * 
     * @param string $address        	
     * @param array $post        	
     * @return mixed
     */
    public function process($address, $post)
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $address);
        curl_setopt($curl, CURLOPT_FAILONERROR, true);
        // Validate Cert
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, true);
        // Validate Server
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2);
        // Tell the Cert Location
        curl_setopt($curl, CURLOPT_CAINFO, WEBROOT_DIR . "/VeriSignClass3PublicPrimaryCertificationAuthority-G5.crt");
        curl_setopt($curl, CURLOPT_SSLVERSION, 3);
        // Set Connection timeout
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 10);
        // Set timeout
        curl_setopt($curl, CURLOPT_TIMEOUT, 40);
        // Force Return
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $post);

        $result = curl_exec($curl);

        if ($result) {
            curl_close($curl);
        } else {
            $result = curl_error($curl);
        }
        return $result;
    }

    /**
     * Verify Error
     *
     * @param string $post        	
     * @param string $response        	
     * @throws Exception
     * @return boolean
     */
    public function verifyError($post, $response)
    {
        $error_msg = null;

        try {
            if (stripos($response, "SSL certificate problem") !== false) {
                throw new Exception("CERTIFICADO INVÁLIDO - O certificado da transação não foi aprovado", "099");
            }

            $result = simplexml_load_string($response, null, LIBXML_NOERROR);
            if ($result == null) {
                throw new Exception("HTTP READ TIMEOUT - o Limite de Tempo da transação foi estourado", "099");
            }
        } catch (Exception $ex) {
            $error_msg = "     Código do erro: " . $ex->getCode() . "\n";
            $error_msg .= "     Mensagem: " . $ex->getMessage() . "\n";

            // Generate HTML Page
            echo '<html><head><title>Erro na transação</title></head><body>';
            echo '<span style="color:red;, font-weight:bold;">Ocorreu um erro em sua transação!</span>' . '<br />';
            echo '<span style="font-weight:bold;">Detalhes do erro:</span>' . '<br />';
            echo '<pre>' . $error_msg . '<br /><br />';
            echo '</pre><p><center>';
            echo '</center></p></body></html>';
            $error_msg .= "     XML de envio: " . "\n" . $post;
            echo $error_msg;
            return true;
        }

        if ($result->getName() == "erro") {
            $error_msg = "     Código do erro: " . $result->codigo . "\n";
            $error_msg .= "     Mensagem: " . utf8_decode($result->mensagem) . "\n";
            // Gera p�gina HTML
            echo '<html><head><title>Erro na transação</title></head><body>';
            echo '<span style="color:red; font-weight:bold;">Ocorreu um erro em sua transação!</span>' . '<br />';
            echo '<span style="font-weight:bold;">Detalhes do erro:</span>' . '<br />';
            echo '<pre>' . $error_msg . '<br /><br />';
            echo '</pre><p><center>';
            echo '</center></p></body></html>';
            $error_msg .= "     XML de envio: " . "\n" . $post;
        }
    }
}
