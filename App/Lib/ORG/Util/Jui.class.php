<?php

class Jui extends Action
{
    public $RESULT_SUCCESS = 200;
    public $RESULT_FAIL = 300;
    public $RESULT_AUTH = 301;
    public $RESULT_ERROR = 400;  //暂时不用
    //const RESULT_EXCEPTION=500; //暂时不用

    public $CALLBACK_TYPE_CLOSE = 'closeCurrent';
    public $CALLBACK_TYPE_FORWARD = 'forward';
    public $CALLBACK_TYPE_FORWARD_CONFIRM = 'forwardConfirm';

    /**
     * "statusCode":"200",
     * "message":"操作成功",
     * "navTabId":"",   //需要更新的tab  默认规则【model-action】一般刷新list页面
     * "rel":"",    //用于局部刷新div id号
     * "dialogRel":"",   //用户刷新dialog
     * "callbackType":"closeCurrent",
     * "forwardUrl":""
     * "confirmMsg":""
     */

    public function listPagerForm()
    {
        $formHtml = '<form id="pagerForm" method="post" action="">
                        <input type="hidden" name="status" value=""><input type="hidden" name="keywords" value="" />
                        <input type="hidden" name="pageNum" value="1" /><input type="hidden" name="numPerPage" value="" />
                        <input type="hidden" name="orderField" value="" /><input type="hidden" name="orderDirection" value=""/></form>';
        return $formHtml;

    }

    public function listPanelBar()
    {

    }

    public function listThead()
    {

    }

    public function ListBody()
    {

    }

    /**
     * 分页
     * @param $pagination
     * @param string $targetType
     * @param string $rel
     * @return string
     */
    public function  ListPagination($pagination, $targetType = 'navTab', $rel = 'pagebanel')
    {
        $listHtml = '<div class="panelBar"><div class="pages"><span>每页' . $pagination->listRows . '条，  共' . $pagination->totalRows . '条</span> </div>';
        $listHtml .= '<div class="pagination"   targetType="' . $targetType . '" totalCount="' . $pagination->totalRows . '"
             numPerPage="' . $pagination->listRows . '"   pageNumShown="10"  currentPage="' . $pagination->nowPage . '"></div></div>';

        return $listHtml;
    }

    public function setJuiResult($result, $variables = null, $message = '', $callbackType = 'closeCurrent', $formMessage = null)
    {
        if (is_bool($variables) || null == $variables) {
            $variables = array();
        }
        switch ($result) {
            case $this->RESULT_SUCCESS:
                if (empty($message)) {
                    $message = '操作成功！';
                }
                break;
            case $this->RESULT_FAIL:
                if (empty($message)) {
                    $message = '操作失败！';
                }
                break;
            case $this->RESULT_ERROR:
                if (empty($message)) {
                    $message = '异常操作，请报告管理员！';
                }
                $result = $this->RESULT_FAIL;
                break;
        }
        if (!isset($variables['statusCode'])) {
            $variables['statusCode'] = $result;
        }
        if (!isset($variables['message']) && !empty($message)) {
            $variables['message'] = $message;
        }
        if (!isset($variables['callbackType']) && !empty($callbackType)) {
            $variables['callbackType'] = $callbackType;
        }
        if (!isset($variables['formMessage']) && null != $formMessage) {
            $variables['formMessage'] = $formMessage;
        }
        $this->result($variables);
        // 返回JSON数据格式到客户端 包含状态信息
     // {"statusCode":300,"message":"\u8bf7\u9009\u62e9\u6709\u6548\u7684\u5730\u5740\uff01"}
    }
    public function  result($variables){
        header("Content-Type:text/html; charset=utf-8");
        exit(json_encode($variables));

    }


}