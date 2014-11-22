(function($){
    $.extend({});
    $.fn.extend({
        selectedToDialog: function(){
            function _formatData(selectedIdName,ids,postType){
                var mapData={};
                if (postType == 'map'){
                    mapData[selectedIdName]=ids;
                } else {
                    mapData[selectedIdName] = ids.join(",");
                }
                return mapData;
            };
            return this.each(function(){
                var $this = $(this);
                var selectedIdName = $this.attr("idRel") || "ids";
                var dialogTitle = $this.attr("dTitle") || $this.text();
                var rel = $this.attr("rel") || "_blank";
                var options = {};
                var w = $this.attr("width");
                var h = $this.attr("height");
                if (w) options.width = w;
                if (h) options.height = h;
                options.max = eval($this.attr("max") || "false");
                options.mask = eval($this.attr("mask") || "false");
                options.maxable = eval($this.attr("maxable") || "true");
                options.minable = eval($this.attr("minable") || "true");
                options.fresh = eval($this.attr("fresh") || "true");
                options.resizable = eval($this.attr("resizable") || "true");
                options.drawable = eval($this.attr("drawable") || "true");
                options.close = eval($this.attr("close") || "");
                options.param = $this.attr("param") || "";
                options.callback = $this.attr("callback") || null;
                options.type=$this.attr("type") || "POST";
                $this.click(function(){
                    var targetType = $this.attr("targetType");
                    var ids = _getIds(selectedIdName, targetType);
                    if (0==ids.length) {
                        if($this.attr("form")){   //自动获取form参数
                            var $p = targetType == "dialog" ? $.pdialog.getCurrent() : navTab.getCurrentPanel();
                            var $form = $("#pagerForm", $p);
                            options.data =$form.serialize();
                        }else{
                            alertMsg.error($this.attr("warn") || DWZ.msg("alertSelectMsg"));
                            return false;
                        }
                    }else{
                        var postType = $this.attr("postType") || "map";
                        options.data = _formatData(selectedIdName,ids, postType);
                    }
                    function _doPost() {
                        $.pdialog.open($this.attr('href'), rel, dialogTitle, options);
                    }
                    var title = $this.attr("title");
                    if (title) {
                        var count=0;
                        if (0==ids.length) {
                            count=$this.attr("count")||-1;
                        }else{
                            count=ids.length;
                        }
                        if(-1!=count){
                            title="共"+count+"条记录，"+title;
                        }
                        alertMsg.confirm(title, {okCall: _doPost});
                    } else {
                        _doPost();
                    }
                    return false;
                });
            });
        },
        d1mExport: function(){
            function _doExport($this,selectedIdName,targetType,ids) {
                var url = $this.attr("href");
                var $p = targetType == "dialog" ? $.pdialog.getCurrent() : navTab.getCurrentPanel();
                var $form = $("#pagerForm", $p);
                var exportForm=$form.clone().attr({id:"",target:"_blank",action:url});
                if (ids.length>0) {
                    var inputDom=[];
                    $.each(ids,function(i,id){
                        inputDom.push('<input type="hidden" name="Ids[]" value="'+id+'"/>');
                    });
                    exportForm.append(inputDom.join(','));
                }
                $p.append(exportForm);
                exportForm.submit();
                exportForm.remove();
            }
            return this.each(function(){
                var $this = $(this);
                $this.click(function(event){
                    var title = $this.attr("title");
                    var targetType=$this.attr("targetType");
                    var selectedIdName = $this.attr("rel") || "ids";
                    var ids=_getIds(selectedIdName,targetType);
                    if (title) {
                        var count=0;
                        if (0==ids.length) {
                            count=$this.attr("count")||-1;
                        }else{
                            count=ids.length;
                        }
                        if(-1!=count){
                            title="共"+count+"条记录，"+title;
                        }
                        alertMsg.confirm(title, {
                            okCall: function(){_doExport($this,selectedIdName,targetType,ids);}
                        });
                    } else {_doExport($this,selectedIdName,targetType,ids);}

                    event.preventDefault();
                });
            });
        }
    });
    function _getIds(selectedIdName, targetType){
        var ids = [];
        var $box;
        if("dialog"==targetType){
            $box = $.pdialog.getCurrent();
        }else{
            $box = $("#"+targetType);
            if(0==$box.length){
                $box=navTab.getCurrentPanel();
            }
        }
        $box.find("input:checked").filter("[name='"+selectedIdName+"']").each(function(i){
            var val = $(this).val();
            //ids += i==0 ? val : ","+val;
            ids.push(val);
        });
        return ids;
    };
})(jQuery);
function selectAutoFill(selectDom,formElement){
    var selectObj=$(selectDom);
    var formObj=selectObj.parents('form');
    $("input[name='"+formElement+"'],select[name='"+formElement+"']",formObj).val(function(){
        var $this=$(this);
        var val=selectObj.val();
        if('radio'==$this.attr('type')){
            return [val];
        }
        return val;
    });
}