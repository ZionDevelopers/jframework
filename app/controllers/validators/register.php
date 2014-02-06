<?php 
/**
 * jFramework
 * @copyright Copyright (c) 2010-2014, Júlio César de Oliveira
 * @author Júlio César de Oliveira <talk@juliocesar.me>
 * @license http://www.apache.org/licenses/LICENSE-2.0.html Apache 2.0 License
 */

$val = false;
if (! empty ( $_POST )) {
	$html = "<ul>";
	
	$_POST ['profile'] ['doc'] = preg_replace ( "/([^0-9])/", "",$_POST ['profile'] ['doc']);
	$_POST ['profile'] ['doc2'] = preg_replace ( "/([^0-9])/", "",$_POST ['profile'] ['doc2']);
	
	if (isset ( $_POST ['account'] ['person_type_id'] )) {
		$_POST ['account'] ['person_type_id'] = ( int ) $_POST ['account'] ['person_type_id'];
		if ($_POST ['account'] ['person_type_id'] != 1 && $_POST ['account'] ['person_type_id'] != 2) {
			$_POST ['account'] ['person_type_id'] = 1;
		}
	}
	
	$testEmail = $db->find ( 'accounts', array (), array ( 'email' => $_POST ['account'] ['email'] ) );
	
	// Validate Email
	if (empty ( $_POST ['account'] ['email'] )) {
		$html .= "\n<li>Digite seu e-mail</li>";
	} elseif (! tools::validateEmail ( $_POST ['account'] ['email'] )) {
		$html .= "\n<li>Digite um e-mail válido</li>";
	} elseif (! empty ( $testEmail )) {
		$html .= "\n<li>Este e-mail já em uso</li>";
	}
	
	// Validate Password
	if (empty ( $_POST ['account'] ['password'] )) {
		$html .= "\n<li>Digite sua senha</li>";
	} elseif (strlen ( $_POST ['account'] ['password'] ) <= 5) {
		$html .= "\n<li>Sua senha precisa conter mais de 5 caracteres</li>";
	}
	
	// Validate Password2
	if (empty ( $_POST ['account'] ['password2'] )) {
		$html .= "\n<li>Confirme sua senha</li>";
	} elseif ($_POST ['account'] ['password2'] != $_POST ['account'] ['password']) {
		$html .= "\n<li>Confirme sua senha</li>";
	}
	
	// -------------- If is not a person
	if ($_POST ['account'] ['person_type_id'] == 2) {
		// Validate Company
		if (empty ( $_POST ['profile'] ['name_company'] )) {
			$html .= "\n<li>Digite a razão social</li>";
		}
		
		// Validate Contact
		if (empty ( $_POST ['profile'] ['surname_contact'] )) {
			$html .= "\n<li>Digite o nome do contato</li>";
		}
		
		$testCNPJ = $db->find ( 'profiles', array (), array ( 'doc' => $_POST ['profile'] ['doc'] ) );
		
		// Validate DOC
		if (empty ( $_POST ['profile'] ['doc'] )) {
			$html .= "\n<li>Digite o número do CNPJ</li>";
		} elseif (! tools::validateCNPJ ( $_POST ['profile'] ['doc'] )) {
			$html .= "\n<li>Digite um número de CNPJ válido</li>";
		} elseif (! empty ( $testCNPJ )) {
			$html .= "\n<li>Este número de CNPJ já em uso</li>";
		}
		
		$testCRECI = $db->find ( 'profiles', array (), array ( 'doc2' => $_POST ['profile'] ['doc2'] ) );
		
		// Validate DOC2
		if (empty ( $_POST ['profile'] ['doc2'] )) {
			$html .= "\n<li>Digite o número do CRECI</li>";
		} elseif (strlen ( $_POST ['profile'] ['doc2'] ) != 6) {
			$html .= "\n<li>Digite um número de CRECI válido</li>";
		} elseif (! empty ( $testCRECI )) {
			$html .= "\n<li>Este número de CRECI já em uso</li>";
		}
		
		// Validate Company Fundation Date
		if (empty ( $_POST ['profile'] ['birthdate_day'] )) {
			$html .= "\n<li>Selecione o dia da Data de fundação</li>";
		}
		// Validate Contact
		if (empty ( $_POST ['profile'] ['birthdate_month'] )) {
			$html .= "\n<li>Selecione o mês da Data de fundação</li>";
		}
		// Validate Contact
		if (empty ( $_POST ['profile'] ['birthdate_year'] )) {
			$html .= "\n<li>Selecione o ano da Data de fundação</li>";
		}
	} else { // ------------- If Is A PERSON
	         // Validate Name
		if (empty ( $_POST ['profile'] ['name_company'] )) {
			$html .= "\n<li>Digite o seu nome</li>";
		}
		
		// Validate Surname
		if (empty ( $_POST ['profile'] ['surname_contact'] )) {
			$html .= "\n<li>Digite o sobrenome</li>";
		}
		
		$testCPF = $db->find ( 'profiles', array (), array ( 'doc' => $_POST ['profile'] ['doc'] ) );
		
		// Validate DOC
		if (empty ( $_POST ['profile'] ['doc'] )) {
			$html .= "\n<li>Digite o número do CPF</li>";
		} elseif (! tools::validateCPF ( $_POST ['profile'] ['doc'] )) {
			$html .= "\n<li>Digite um número de CPF válido</li>";
		} elseif (! empty ( $testCPF )) {
			$html .= "\n<li>Este número de CPF já em uso</li>";
		}
		
		$testRG = $db->find ( 'profiles', array (), array ( 'doc' => $_POST ['profile'] ['doc2'] ) );
		
		// Validate DOC2
		if (empty ( $_POST ['profile'] ['doc2'] )) {
			$html .= "\n<li>Digite o número do RG</li>";
		} elseif (! empty ( $testRG )) {
			$html .= "\n<li>Este número de RG já em uso</li>";
		}
		
		// Validate Company Fundation Date
		if (empty ( $_POST ['profile'] ['birthdate_day'] )) {
			$html .= "\n<li>Selecione o dia da Data de nascimento</li>";
		}
		// Validate Contact
		if (empty ( $_POST ['profile'] ['birthdate_month'] )) {
			$html .= "\n<li>Selecione o mês da Data de nascimento</li>";
		}
		// Validate Contact
		if (empty ( $_POST ['profile'] ['birthdate_year'] )) {
			$html .= "\n<li>Selecione o ano da Data de nascimento</li>";
		}
	}
	
	// Validate Mobile Code
	if (empty ( $_POST ['profile'] ['phone_code'] )) {
		$html .= "\n<li>Digite o ddd do telefone</li>";
	}
	
	// Validate Mobile
	if (empty ( $_POST ['profile'] ['phone'] )) {
		$html .= "\n<li>Digite seu número de telefone</li>";
	}
	
	// Validate Address Type
	if (empty ( $_POST ['address'] ['address_type'] )) {
		$html .= "\n<li>Selecione o tipo de endereço</li>";
	}
	
	// Validate Postal Code
	if (empty ( $_POST ['address'] ['postalcode'] )) {
		$html .= "\n<li>Digite o CEP</li>";
	} elseif (strlen ( $_POST ['address'] ['postalcode'] ) != 8) {
		$html .= "\n<li>Digite o CEP corretamente</li>";
	}
	
	// Validate Address
	if (empty ( $_POST ['address'] ['address'] )) {
		$html .= "\n<li>Digite o endereço</li>";
	}
	
	// Validate Address Number
	if (empty ( $_POST ['address'] ['address_number'] )) {
		$html .= "\n<li>Digite o número do endereço</li>";
	}
	
	// Validate District
	if (empty ( $_POST ['address'] ['district'] )) {
		$html .= "\n<li>Digite o bairro</li>";
	}
	
	// Validate City
	if (empty ( $_POST ['address'] ['city'] )) {
		$html .= "\n<li>Digite a cidade</li>";
	}
	
	// Validate State
	if (empty ( $_POST ['address'] ['state'] )) {
		$html .= "\n<li>Selecione o estado</li>";
	}
	
	// Validate Captcha
	if (! empty ( $_POST ['captcha'] ) && isset ( $_SESSION ['SYSTEM'] ['CAPTCHA'] ['register'] )) {
		if (strtolower ( $_POST ['captcha'] ) != $_SESSION ['SYSTEM'] ['CAPTCHA'] ['register']) {
			$html .= "\n<li>Verificação de segurança inválida </li>";
		}
	} else {
		$html .= "\n<li>Digite os carecteres da imagem da verificação de segurança</li>";
	}
	
	$html .= "</ul>";
	
	if ($html != "<ul></ul>" && isset ( $form )) {
		echo $html;
	} elseif ($html == "<ul></ul>" && ! isset ( $form )) {
		$val = true;
	}
}
