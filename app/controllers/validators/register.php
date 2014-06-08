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

// Form validation example

$val = false;

if (!empty($_POST)) {
    $html = "<ul>";

    $testEmail = $db->find('accounts', array(), array('email' => $_POST ['account'] ['email']));

    // Validate Email
    if (empty($_POST ['account'] ['email'])) {
        $html .= "\n<li>Type your e-mail</li>";
    } elseif (!empty($testEmail)) {
        $html .= "\n<li>This e-mail is already being used</li>";
    }

    // Validate Name
    if (empty($_POST ['profile'] ['name'])) {
        $html .= "\n<li>Type your name</li>";
    }

    // Validate Surname
    if (empty($_POST ['profile'] ['surname'])) {
        $html .= "\n<li>Type your surname</li>";
    }

    $html .= "</ul>";

    if ($html != "<ul></ul>" && isset($form)) {
        echo $html;
    } elseif ($html == "<ul></ul>" && !isset($form)) {
        $val = true;
    }
}
