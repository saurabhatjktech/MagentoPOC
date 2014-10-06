<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Jkt
 * @package     Jkt_Social
 * @copyright   Copyright (c) 2014 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Jkt_Social_Block_Adminhtml_System_Config_Fieldset_General
    extends Mage_Adminhtml_Block_System_Config_Form_Fieldset
{
    protected function _getHeaderHtml($element)
    {
        if (method_exists($this, '_getHeaderTitleHtml')){
            if ($element->getIsNested()) {
                $html = '<tr class="nested"><td colspan="4"><div class="' . $this->_getFrontendClass($element) . '">';
            } else {
                $html = '<div class="' . $this->_getFrontendClass($element) . '">';
            }
            $html .= $this->_getHeaderTitleHtml($element);

            $html .= '<input id="'.$element->getHtmlId() . '-state" name="config_state[' . $element->getId()
                . ']" type="hidden" value="' . (int)$this->_getCollapseState($element) . '" />';
            $html .= '<fieldset class="' . $this->_getFieldsetCss($element) . '" id="' . $element->getHtmlId() . '">';
            //$html.= '<button style="margin-right:25px; margin-top:-33px;  float: right;" onclick="window.open(\'http://wiki.jkt.com/display/sc/Social+Connector+Home\')" class="scalable go" type="button" id="gsc_wiki"><span>'.$this->__('Jkt Social Connector Wiki').'</span></button>';

            $html .= '<legend>' . $element->getLegend() . '</legend>';

            $html .= $this->_getHeaderCommentHtml($element);

            // field label column
            $html .= '<table cellspacing="0" class="form-list"><colgroup class="label" /><colgroup class="value" />';
            if ($this->getRequest()->getParam('website') || $this->getRequest()->getParam('store')) {
                $html .= '<colgroup class="use-default" />';
            }
            $html .= '<colgroup class="scope-label" /><colgroup class="" /><tbody>';
        }else{
            $default = !$this->getRequest()->getParam('website') && !$this->getRequest()->getParam('store');
            $html = '<div  class="entry-edit-head collapseable" >';
            //$html.= '<button style="margin-right:25px;float: right;" onclick="window.open(\'http://wiki.jkt.com/display/sc/Social+Connector+Home\')" class="scalable go" type="button" id="gsc_wiki"><span>'.$this->__('Jkt Social Connector Wiki').'</span></button>';
            $html.= '<a id="'.$element->getHtmlId().'-head" href="#" onclick="Fieldset.toggleCollapse(\''.$element->getHtmlId().'\', \''.$this->getUrl('*/*/state').'\'); return false;">'.$element->getLegend().'</a></div>';
            $html.= '<input id="'.$element->getHtmlId().'-state" name="config_state['.$element->getId().']" type="hidden" value="'.(int)$this->_getCollapseState($element).'" />';
            $html.= '<fieldset class="'.$this->_getFieldsetCss().'" id="'.$element->getHtmlId().'">';
            $html.= '<legend>'.$element->getLegend().'</legend>';

            if ($element->getComment()) {
                $html .= '<div class="comment">'.$element->getComment().'</div>';
            }
            // field label column
            $html.= '<table cellspacing="0" class="form-list"><colgroup class="label" /><colgroup class="value" />';
            if (!$default) {
                $html.= '<colgroup class="use-default" />';
            }
            $html.= '<colgroup class="scope-label" /><colgroup class="" /><tbody>';
        }
        return $html;
    }

    protected function _getFieldsetCss($element = null)
    {
        $configCss = (string)$this->getGroup($element)->fieldset_css;
        return 'config collapseable'.($configCss ? ' ' . $configCss : '');
    }

}
