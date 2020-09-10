<?php

namespace phpCollab\Login\Languages;


use Exception;
use Symfony\Component\Translation\Loader\PhpFileLoader;
use Symfony\Component\Translation\Translator;

class Languages
{
    protected $trans;
    protected $language;
//    protected $loaderType;


    //$translator->addLoader('php', new \Symfony\Component\Translation\Loader\PhpFileLoader());


    public function __construct()
    {
//        $this->language = $lang;
//        $this->trans = new Translator($lang);
//        $this->setLoader();
//        $this->loadLanguageFiles();
    }

    public function initLanguage()
    {
        try {
            $this->trans = new Translator($this->language);
            $this->setLoader();
            $this->loadLanguageFiles();
        } catch (Exception $e) {
            xdebug_var_dump($e);
        }
    }

    public function setLanguage($lang)
    {
        $this->language = $lang;
        $this->initLanguage();
        return $this->language;
    }

    private function setLoader()
    {
        $this->trans->addLoader('php', new PhpFileLoader());
    }

    public function loadLanguageFiles()
    {
        // Special check for a few languages that don't folloow normal convention
//        if (in_array($this->language, ['cs', 'sk'])) {
////            xdebug_var_dump('Check additional language files for ' . $this->language);
////            switch ($this->language) {
////                case 'cs':
////            if ($this->language === 'cs') {
////                $this->trans->addResource('php', APP_ROOT . '/translations/lang_cs.php', $this->language);
////            }
//
//            if ($this->language === 'sk') {
//                $this->trans->addResource('php', APP_ROOT . '//languages/lang_sk.php', $this->language);
//            }
//            return $this;
//        } else {
        $this->trans->addResource('php',
            APP_ROOT . '/translations/messages.' . strtolower(str_replace('-', '_', $this->language)) . '.php',
            $this->language);
        return $this;
//        }

//        xdebug_var_dump(APP_ROOT);
    }

    public function checkLanguageFiles($language)
    {
        if (file_exists(APP_ROOT . "/languages/lang_" . $language . ".php")) {
            return true;
        } else {
            return false;
        }
    }

    public function getLanguage()
    {
        return $this->language;
    }

    public function getTranslator()
    {
        return $this->trans;
    }

    public function getLanguages()
    {
        return [
            "ar" => "Arabic",
            "az" => "Azerbaijani",
            "bg" => "Bulgarian",
            "ca" => "Catalan",
            "cs-iso" => "Czech (iso)",
            "cs-win1250" => "Czech (win1250)",
            "da" => "Danish",
            "de" => "German",
            "en" => "English",
            "es" => "Spanish",
            "et" => "Estonian",
            "fr" => "French",
            "hu" => "Hungarian",
            "in" => "Indonesian",
            "is" => "Icelandic",
            "it" => "Italian",
            "ja" => "Japanese",
            "ko" => "Korean",
            "lv" => "Latvian",
            "nl" => "Dutch",
            "no" => "Norwegian",
            "pl" => "Polish",
            "pt" => "Portuguese",
            "pt-br" => "Brazilian Portuguese",
            "ro" => "Romanian",
            "ru" => "Russian",
            "sk-win1250" => "Slovak (win1250)",
            "tr" => "Turkish",
            "uk" => "Ukrainian",
            "zh" => "Chinese simplified",
            "zh-tw" => "Chinese traditional",
        ];
    }

}