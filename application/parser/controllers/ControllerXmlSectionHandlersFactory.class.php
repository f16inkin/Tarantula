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
        //Объекты SimpleXmlElement
        $SXEs = $this->_xml_reports_handler->loadCorrectXml();
        //Содержит объекты обработанных Xml файлов
        $handled = [];
        if (!empty($SXEs)){
            $iterator = 0;
            foreach ($SXEs as $SXE){
                $iterator ++;
                $handled[$iterator] = $this->_xml_section_handlers_factory->handle($SXE['simpleXmlElement']);
                $content[$iterator]['session'] = $handled[$iterator]->_sessions;
                $content[$iterator]['tanks'] = $handled[$iterator]->_tanks;
            }
            $this->loadPage('/parser/ajax/successed/main/step-3/step-3.page', $content);
        }else{
            $content['upload_limit'] = ini_get('max_file_uploads');
            $this->loadPage('/parser/ajax/successed/main/step-1/step-1.page', $content);
        }

    }

}