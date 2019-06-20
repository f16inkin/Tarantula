<?php


namespace application\parser\controllers;


use application\parser\base\ControllerParserBase;
use application\parser\models\XmlReportsHandler;
use application\parser\models\XmlSectionHandlersFactory;

class ControllerXmlSectionHandlersFactory extends ControllerParserBase
{
    private $_xml_reports_handler;
    private $_xml_section_handlers_factory;

    public function __construct(int $subdivision_id)
    {
        parent::__construct();
        $this->_xml_reports_handler = new XmlReportsHandler($this->_settings->getStorage());
        $this->_xml_section_handlers_factory = new XmlSectionHandlersFactory($subdivision_id);
    }

    public function actionIndex()
    {
        $SXEs = $this->_xml_reports_handler->loadCorrectXml();
        $i = 0;
        foreach ($SXEs as $SXE){
            $i ++;
            $handled[$i] = $this->_xml_section_handlers_factory->handle($SXE['simpleXmlElement']);
        }
        foreach ($handled as $item) {
            echo 'Смена начало';
            echo '<pre>';
            print_r($item->_sessions);
            print_r($item->_tanks);
            echo '</pre>';
            echo 'Смена конец';
            echo '<br>';
        }
    }

}