<?php

namespace \jFramework\Core\EmailTemplateParser;

/**
 * Parse HTML templates by replacing vars with it's contents
 * 
 * @author Júlio Oliveira <talk@juliocesar.me>
 * @copyright (c) 2016, Júlio Oliveira
 * @version 1.1.1
 */
class EmailTemplateParser
{
    /**
     * Regexes
     */
    CONST VAR_REGEX = '/\$([a-zA-Z\-_.]+)\$/';
    CONST IF_ENDIF_REGEX = '/@IF\(\$([a-zA-Z\-_.]+)\$ ([=><!]+) [\'"]?([a-zA-Z0-9,.\s\-]+)[\'"]?\)@([^@END]+)@ENDIF@/';
    CONST IF_ENDIF_CHECK = '/@IF|@ENDIF@/';
    
    /**
     * E-mail Template Location
     * @var string
     */
    private $filename = '';
    
    /**
     * Array with all the variables to be parsed
     * @var array
     */
    private $vars = [];
    
    /**
     * Template contents
     * @var string
     */
    private $templateContents = '';
    
    /**
     * Class constructor
     * @param string $filename
     */
    public function __construct($filename) {
        // Add file location to the object
        $this->setFilename($filename);
    }
    
    /**
     * Set an array of variables to be parsed
     * @param array $vars
     */
    public function setVars(array $vars = []) {
        // Add vars to the object by merging existing keys
        $this->vars = array_merge($this->vars, $vars);
    }
   
    /**
     * Set e-mail template filename
     * @throws Exception
     * @param string $filename
     */
    public function setFilename($filename) {
        // Check if file is readable
        if ($this->isTemplateValid($filename)) {
            // Set filename to the object
            $this->filename = $filename;
        } else {
            // Throw an exception
            throw new Exception('Error: File "'. $filename.'" does not exists or couldn\'t be read', 404);
        }
    }
    
    /**
     * Check if file is a valid HTML e-mail template
     * @param string $filename
     * @return boolean
     */
    public function isTemplateValid($filename) {
        return is_readable($filename);
    }
    
    /**
     * Internal function to be used on preg_replace_callback
     * @param array $matches
     */
    private function varParser($matches) {
        // Check for regex result
        if (isset($matches[1])) {
            // Check if match is set
            if (isset($this->vars[$matches[1]])) {
                // Replace var with content
                $result = str_replace($matches[1], $this->vars[$matches[1]], $matches[1]);
            } else {
                // Return var name
                $result = '$' . $matches[1] . '$';
            }
        }
        
        return $result;
    }
    
    /**
     * 
     * @param type $matches
     */
    private function ifParser($matches) {
        // Set result
        $result = '';
        
        // Check for matches
        if(isset($matches[1]) && isset($matches[2]) && isset($matches[3]) && isset($matches[4])) {
            // Define used vars
            $var = $matches[1];
            $op = $matches[2];
            $value = $matches[3];
            $code = $matches[4];            
            $result = $matches[1];

            // Check if IF var exists and if operator is valid
            if (isset($this->vars[$var]) && ($op === '==' || $op === '>=' || $op === '<=' || $op === '>' || $op === '<' || $op === '!=')) {
                // Traslate variable
                $var = $this->vars[$var];
                
                // Operator equals to
                if ($op === '=='){
                   // Check if value is -1, that equals to empty
                    if($value == -1) {
                        // Check if var is empty
                        if (empty($var)) {
                            // Parse variables
                           $result = $this->parseVars($code); 
                        } else {
                            $result = '';
                        }

                    } else {
                        // Check if var is equal to value
                        if ($var == $value) {
                            // Parse variables
                           $result = $this->parseVars($code); 
                        } else {
                            $result = '';
                        }
                    }
                // Operator greater or equal
                } elseif ($op === '>='){
                    // Check if var is equal to value
                    if ($var >= $value) {
                        // Parse variables
                       $result = $this->parseVars($code); 
                    } else {
                        $result = '';
                    }
                // Operator lesser or equal
                } elseif ($op === '<='){
                    // Check if var is equal to value
                    if ($var <= $value) {
                        // Parse variables
                       $result = $this->parseVars($code); 
                    } else {
                        $result = '';
                    }
                // Operator greater then
                } elseif ($op === '>'){
                    // Check if var is equal to value
                    if ($var > $value) {
                        // Parse variables
                       $result = $this->parseVars($code); 
                    } else {
                        $result = '';
                    }
                // Operator lesser then
                } elseif ($op === '<'){
                    // Check if var is equal to value
                    if ($var < $value) {
                        // Parse variables
                       $result = $this->parseVars($code); 
                    } else {
                        $result = '';
                    }
                // Operator not equal thne
                } elseif ($op === '!='){
                    // Check if value is -1, that equals to !empty
                    if($value == -1) {
                        // Check if var not empty
                        if (!empty($var)) {
                            // Parse variables
                           $result = $this->parseVars($code); 
                        } else {
                            $result = '';
                        }
                    } else {
                        // Check if var is equal to value
                        if ($var != $value) {
                            // Parse variables
                           $result = $this->parseVars($code); 
                        } else {
                            $result = '';
                        }
                    }
                }
            }
        }
        
        return $result;
    }
    
    /**
     * IFs parser
     */
    private function parseIFs(){
        // Parse IFs
        $this->templateContents = preg_replace_callback(self::IF_ENDIF_REGEX, [$this, 'ifParser'], $this->templateContents);
    }
    
    /**
     * Variables parser
     */
    private function parseVars($code = NULL) {
        if (is_null($code)) {
            // Parse variables
            $this->templateContents = preg_replace_callback(self::VAR_REGEX, [$this, 'varParser'], $this->templateContents);
        } else {
            // Parse variables
            $code = preg_replace_callback(self::VAR_REGEX, [$this, 'varParser'], $code);
        }
        
        return is_null($code) ? $this->templateContents : $code;
    }
    
    /**
     * Runs the Parser preg REGEX
     */
    public function parse() {
        // Check if file is valid and is there any variable to parse
        if ($this->isTemplateValid($this->filename) && !empty($this->vars)) {
            // Get e-mail template contents
            $this->templateContents = file_get_contents($this->filename);
            
            // Check for html IFs
            $foundIFs = preg_match(self::IF_ENDIF_CHECK, $this->templateContents);
            
            // Check for HTML vars
            if ($foundIFs) {  
                // Parse IFs
                $this->parseIFs();
            }
            
            // Check for html vars
            $foundVars = preg_match(self::VAR_REGEX, $this->templateContents);
            
            // Check for HTML vars
            if (!$foundVars) {
                // Throw an exception
                throw new Exception('Error: You didn\'t set any variables on the HTML file to be parsed yet!"'. $this->filename.'" !', 404);
            } else {   
                // Parse variables
                $this->parseVars();
            }            
            
        // Check if file is not valid
        } elseif (!$this->isTemplateValid($this->filename)) {
            // Throw an exception
            throw new Exception('Error: File "'. $this->filename.'" does not exists or couldn\'t be read', 404);
        // No vars to be parsed
        } else {
            // Throw an exception
            throw new Exception('Error: You didn\'t set any variables to be parsed yet!"'. $this->filename.'" !', 404);
        }
    }
    
    /**
     * Get parsed or not template code
     * @return string
     */
    public function getTemplateCode() {
        return $this->templateContents;
    }
    
    /**
     * Write template content
     * @param string $filename
     * @return boolean
     */
    public function writeTemplateToFile($filename) {
        return file_put_contents($filename, $this->templateContents);
    }
}
