(window["webpackJsonp"]=window["webpackJsonp"]||[]).push([["applist_developer"],{"098e":function(e,t,r){},"159b":function(e,t,r){var a=r("da84"),o=r("fdbc"),n=r("17c2"),i=r("9112");for(var s in o){var l=a[s],c=l&&l.prototype;if(c&&c.forEach!==n)try{i(c,"forEach",n)}catch(u){c.forEach=n}}},"17c2":function(e,t,r){"use strict";var a=r("b727").forEach,o=r("a640"),n=o("forEach");e.exports=n?[].forEach:function(e){return a(this,e,arguments.length>1?arguments[1]:void 0)}},"1da1":function(e,t,r){"use strict";r.d(t,"a",(function(){return o}));r("d3b7");function a(e,t,r,a,o,n,i){try{var s=e[n](i),l=s.value}catch(c){return void r(c)}s.done?t(l):Promise.resolve(l).then(a,o)}function o(e){return function(){var t=this,r=arguments;return new Promise((function(o,n){var i=e.apply(t,r);function s(e){a(i,o,n,s,l,"next",e)}function l(e){a(i,o,n,s,l,"throw",e)}s(void 0)}))}}},"62b8":function(e,t,r){"use strict";r.r(t);var a=function(){var e=this,t=e.$createElement,a=e._self._c||t;return a("div",[a("el-breadcrumb",{attrs:{"separator-class":"el-icon-arrow-right"}},[a("el-breadcrumb-item",{attrs:{to:{path:"/home"}}},[e._v("首页")]),a("el-breadcrumb-item",[e._v("开发者中心")]),a("el-breadcrumb-item",[e._v("应用发布")])],1),a("el-card",[a("div",{staticClass:"clearfix",attrs:{slot:"header"},slot:"header"},[a("el-row",{attrs:{gutter:20}},[a("el-col",{attrs:{span:6}},[a("el-input",{attrs:{placeholder:"请输入内容",clearable:""},on:{clear:e.app},model:{value:e.queryInfo.keywords,callback:function(t){e.$set(e.queryInfo,"keywords",t)},expression:"queryInfo.keywords"}},[a("el-select",{staticClass:"select",attrs:{slot:"prepend",placeholder:"请选择"},on:{click:e.app},slot:"prepend",model:{value:e.queryInfo.type,callback:function(t){e.$set(e.queryInfo,"type",t)},expression:"queryInfo.type"}},[a("el-option",{attrs:{label:"全部",value:""}}),a("el-option",{attrs:{label:"插件",value:"0"}}),a("el-option",{attrs:{label:"模板",value:"1"}})],1),a("el-button",{attrs:{slot:"append",icon:"el-icon-search"},on:{click:e.app},slot:"append"})],1)],1)],1)],1),a("el-row",[a("el-button",{attrs:{type:"primary",icon:"el-icon-plus",size:"mini"},on:{click:e.addDialog}},[e._v("发布")])],1),a("el-table",{staticStyle:{width:"100%"},attrs:{data:e.appListData,border:""}},[a("el-table-column",{attrs:{prop:"id",label:"ID",width:"80"}}),a("el-table-column",{attrs:{prop:"name",label:"应用名称"}}),a("el-table-column",{attrs:{prop:"img",label:"缩略图",width:"100",height:"100"},scopedSlots:e._u([{key:"default",fn:function(t){return[t.row.img?a("img",{attrs:{width:"80",src:t.row.img,alt:"缩略图"}}):e._e()]}}])}),a("el-table-column",{attrs:{prop:"type",label:"类型"},scopedSlots:e._u([{key:"default",fn:function(t){return["0"===t.row.type?a("el-tag",{attrs:{type:"success"}},[e._v("插件")]):a("el-tag",[e._v("模板")])]}}])}),a("el-table-column",{attrs:{prop:"is_pay",label:"是否付费"},scopedSlots:e._u([{key:"default",fn:function(t){return["0"===t.row.is_pay?a("el-tag",{attrs:{type:"success"}},[e._v("免费")]):a("el-tag",{attrs:{type:"danger"}},[e._v("￥"+e._s(t.row.money))])]}}])}),a("el-table-column",{attrs:{prop:"download",label:"下载量(单位:次)"}}),a("el-table-column",{attrs:{prop:"status",label:"状态"},scopedSlots:e._u([{key:"default",fn:function(t){return["3"===t.row.status?a("el-tooltip",{attrs:{placement:"top"}},[a("div",{attrs:{slot:"content"},slot:"content"},[e._v("原因："+e._s(t.row.cause))]),a("el-tag",{attrs:{type:"danger"}},[e._v("已驳回")])],1):"2"===t.row.status?a("el-tag",{attrs:{type:"success"}},[e._v("已发布")]):"1"===t.row.status?a("el-tag",{attrs:{type:"info"}},[e._v("审核中")]):"0"===t.row.status?a("el-tag",{attrs:{type:"danger"}},[e._v("已下架")]):e._e()]}}])}),a("el-table-column",{attrs:{prop:"create_time",label:"发布时间"},scopedSlots:e._u([{key:"default",fn:function(t){return[e._v(" "+e._s(e._f("date")(t.row.create_time))+" ")]}}])}),a("el-table-column",{attrs:{label:"操作"},scopedSlots:e._u([{key:"default",fn:function(t){return[a("el-tooltip",{attrs:{enterable:!1,effect:"dark",content:"编辑",placement:"top"}},[a("el-button",{attrs:{type:"primary",icon:"el-icon-edit",size:"mini"},on:{click:function(r){return e.editDialog(t.row.id)}}})],1)]}}])})],1),a("el-pagination",{attrs:{"current-page":e.queryInfo.current_page,"page-sizes":[25,30,40,50],"page-size":e.queryInfo.per_page,layout:"total, sizes, prev, pager, next, jumper",total:e.queryInfo.total},on:{"size-change":e.handleSizeChange,"current-change":e.handleCurrentChange}}),a("el-dialog",{attrs:{title:"发布应用",visible:e.addDialogVisible,width:"50%"},on:{"update:visible":function(t){e.addDialogVisible=t},close:e.addFormClose}},[a("el-form",{ref:"addFormRef",attrs:{model:e.addForm,rules:e.addFormRules,"label-width":"100px"}},[a("el-form-item",{attrs:{label:"类型",prop:"type"}},[a("el-select",{attrs:{placeholder:"请选择应用类型"},model:{value:e.addForm.type,callback:function(t){e.$set(e.addForm,"type",t)},expression:"addForm.type"}},[a("el-option",{attrs:{label:"插件",value:"0"}}),a("el-option",{attrs:{label:"模板",value:"1"}})],1)],1),a("el-form-item",{attrs:{label:"应用名称",prop:"name"}},[a("el-input",{attrs:{placeholder:"请输入应用名称"},model:{value:e.addForm.name,callback:function(t){e.$set(e.addForm,"name",t)},expression:"addForm.name"}})],1),a("el-form-item",{attrs:{label:"缩略图",prop:"img"}},[a("el-upload",{staticClass:"avatar-uploader",attrs:{accept:".jpg,.jpeg,.png,.bmp",action:e.updateUrl,headers:e.headers,"show-file-list":!1,"on-success":e.handleAvatarSuccessAddPhoto,"before-upload":e.beforeAvatarUpload,name:"image"}},[e.addForm.img?a("img",{staticClass:"avatar",attrs:{src:e.addForm.img}}):a("i",{staticClass:"el-icon-plus avatar-uploader-icon"})])],1),a("el-form-item",{attrs:{label:"是否付费",prop:"is_pay"}},[a("el-select",{attrs:{placeholder:"请选择付费类型"},model:{value:e.addForm.is_pay,callback:function(t){e.$set(e.addForm,"is_pay",t)},expression:"addForm.is_pay"}},[a("el-option",{attrs:{label:"免费",value:"0"}}),a("el-option",{attrs:{label:"付费",value:"1"}})],1)],1),"1"===e.addForm.is_pay?a("el-form-item",{attrs:{label:"金额",prop:"money"}},[a("el-input",{attrs:{type:"number",placeholder:"请输入应用付费金额"},model:{value:e.addForm.money,callback:function(t){e.$set(e.addForm,"money",t)},expression:"addForm.money"}})],1):e._e(),a("el-form-item",{attrs:{label:"作者",prop:"author"}},[a("el-input",{attrs:{placeholder:"请输入应用作者"},model:{value:e.addForm.author,callback:function(t){e.$set(e.addForm,"author",t)},expression:"addForm.author"}})],1),a("el-form-item",{attrs:{label:"应用包",prop:"zip"}},[a("el-upload",{staticClass:"avatar-uploader",attrs:{accept:".zip,.rar,.7z",action:e.updateUrl,headers:e.headers,"show-file-list":!1,"on-success":e.handleAvatarSuccessAddFile,"before-upload":e.beforeAvatarUploadAddfile,name:"file"}},[e.addForm.zip?a("img",{staticClass:"avatar",attrs:{src:r("c91b")}}):a("i",{staticClass:"el-icon-plus avatar-uploader-icon"})])],1),a("el-form-item",{attrs:{label:"应用介绍",prop:"introduce"}},[a("mavon-editor",{ref:"addEditorRef",attrs:{boxShadow:!1},on:{imgAdd:e.$imgAdd},model:{value:e.addForm.introduce,callback:function(t){e.$set(e.addForm,"introduce",t)},expression:"addForm.introduce"}})],1)],1),a("span",{staticClass:"dialog-footer",attrs:{slot:"footer"},slot:"footer"},[a("el-button",{on:{click:function(t){e.addDialogVisible=!1}}},[e._v("取 消")]),a("el-button",{attrs:{type:"primary"},on:{click:e.submitAdd}},[e._v("确 定")])],1)],1),a("el-dialog",{attrs:{title:"编辑应用",visible:e.editDialogVisible,width:"50%"},on:{"update:visible":function(t){e.editDialogVisible=t},close:e.editFormClose}},[a("el-form",{ref:"editFormRef",attrs:{model:e.editForm,rules:e.editFormRules,"label-width":"100px"}},[a("el-form-item",{attrs:{label:"类型",prop:"type"}},[a("el-select",{attrs:{placeholder:"请选择应用类型"},model:{value:e.editForm.type,callback:function(t){e.$set(e.editForm,"type",t)},expression:"editForm.type"}},[a("el-option",{attrs:{label:"插件",value:"0"}}),a("el-option",{attrs:{label:"模板",value:"1"}})],1)],1),a("el-form-item",{attrs:{label:"应用名称",prop:"name"}},[a("el-input",{attrs:{placeholder:"请输入应用名称"},model:{value:e.editForm.name,callback:function(t){e.$set(e.editForm,"name",t)},expression:"editForm.name"}})],1),a("el-form-item",{attrs:{label:"缩略图",prop:"img"}},[a("el-upload",{staticClass:"avatar-uploader",attrs:{accept:".jpg,.jpeg,.png,.bmp",action:e.updateUrl,headers:e.headers,"show-file-list":!1,"on-success":e.handleAvatarSuccessEditPhoto,"before-upload":e.beforeAvatarUpload,name:"image"}},[e.editForm.img?a("img",{staticClass:"avatar",attrs:{src:e.editForm.img}}):a("i",{staticClass:"el-icon-plus avatar-uploader-icon"})])],1),a("el-form-item",{attrs:{label:"是否付费",prop:"is_pay"}},[a("el-select",{attrs:{placeholder:"请选择付费类型"},model:{value:e.editForm.is_pay,callback:function(t){e.$set(e.editForm,"is_pay",t)},expression:"editForm.is_pay"}},[a("el-option",{attrs:{label:"免费",value:"0"}}),a("el-option",{attrs:{label:"付费",value:"1"}})],1)],1),"1"===e.editForm.is_pay?a("el-form-item",{attrs:{label:"金额",prop:"money"}},[a("el-input",{attrs:{type:"number",placeholder:"请输入应用付费金额"},model:{value:e.editForm.money,callback:function(t){e.$set(e.editForm,"money",t)},expression:"editForm.money"}})],1):e._e(),a("el-form-item",{attrs:{label:"作者",prop:"author"}},[a("el-input",{attrs:{placeholder:"请输入应用作者"},model:{value:e.editForm.author,callback:function(t){e.$set(e.editForm,"author",t)},expression:"editForm.author"}})],1),a("el-form-item",{attrs:{label:"应用包",prop:"zip"}},[a("el-upload",{staticClass:"avatar-uploader",attrs:{accept:".zip,.rar,.7z",action:e.updateUrl,headers:e.headers,"show-file-list":!1,"on-success":e.handleAvatarSuccessEditFile,name:"file"}},[e.editForm.zip?a("img",{staticClass:"avatar",attrs:{src:r("c91b")}}):a("i",{staticClass:"el-icon-plus avatar-uploader-icon"})])],1),a("el-form-item",{attrs:{label:"应用介绍",prop:"introduce"}},[a("mavon-editor",{ref:"EditEditorRef",attrs:{boxShadow:!1},on:{imgAdd:e.$imgEdit},model:{value:e.editForm.introduce,callback:function(t){e.$set(e.editForm,"introduce",t)},expression:"editForm.introduce"}})],1)],1),a("span",{staticClass:"dialog-footer",attrs:{slot:"footer"},slot:"footer"},[a("el-button",{on:{click:function(t){e.editDialogVisible=!1}}},[e._v("取 消")]),a("el-button",{attrs:{type:"primary"},on:{click:e.submitEdit}},[e._v("确 定")])],1)],1)],1)],1)},o=[],n=r("1da1"),i=(r("b0c0"),r("96cf"),{data:function(){return{queryInfo:{keywords:"",current_page:1,per_page:25,total:0,type:""},appListData:[],addDialogVisible:!1,addForm:{type:"",name:"",img:"",is_pay:"",zip:"",money:"",author:"",introduce:""},addFormRules:{type:[{required:!0,message:"请选择应用类型",trigger:"change"}],name:[{required:!0,message:"请输入应用名称",trigger:"blur"}],img:[{required:!0,message:"请上传应用缩略图",trigger:"blur"}],is_pay:[{required:!0,message:"请选择付费类型",trigger:"change"}],author:[{required:!0,message:"请输入应用作者",trigger:"blur"}],zip:[{required:!0,message:"请上传应用包",trigger:"change"}],introduce:[{required:!0,message:"请输入应用介绍",trigger:"change"}]},editDialogVisible:!1,editForm:{},editFormRules:{type:[{required:!0,message:"请选择应用类型",trigger:"change"}],name:[{required:!0,message:"请输入应用名称",trigger:"blur"}],img:[{required:!0,message:"请上传应用缩略图",trigger:"blur"}],is_pay:[{required:!0,message:"请选择付费类型",trigger:"change"}],author:[{required:!0,message:"请输入应用作者",trigger:"blur"}],zip:[{required:!0,message:"请上传应用包",trigger:"change"}],introduce:[{required:!0,message:"请输入应用介绍",trigger:"change"}]},updateUrl:window.serverConfig.BASE_API+"base/upload",headers:{Authorization:window.sessionStorage.getItem("user_token")}}},created:function(){this.app();var e=this;document.onkeydown=function(t){var r=window.event.keyCode;13===r&&e.app()}},methods:{app:function(){var e=this;return Object(n["a"])(regeneratorRuntime.mark((function t(){var r,a;return regeneratorRuntime.wrap((function(t){while(1)switch(t.prev=t.next){case 0:return t.next=2,e.$http.get("app/list",{params:e.queryInfo});case 2:if(r=t.sent,a=r.data,200===a.code){t.next=6;break}return t.abrupt("return",e.$message.error(a.msg));case 6:e.queryInfo.total=a.data.total,e.appListData=a.data.data;case 8:case"end":return t.stop()}}),t)})))()},addDialog:function(){this.addDialogVisible=!0},submitAdd:function(){var e=this;this.$refs.addFormRef.validate(function(){var t=Object(n["a"])(regeneratorRuntime.mark((function t(r){var a,o;return regeneratorRuntime.wrap((function(t){while(1)switch(t.prev=t.next){case 0:if(r){t.next=2;break}return t.abrupt("return");case 2:return e.addDialogVisible=!1,e.addForm.name=e.$xss(e.addForm.name),e.addForm.author=e.$xss(e.addForm.author),e.addForm.introduce=e.$xss(e.addForm.introduce),"0"===e.addForm.is_pay&&(e.addForm.money=0),t.next=9,e.$http.post("app/add",e.addForm);case 9:if(a=t.sent,o=a.data,201===o.code){t.next=13;break}return t.abrupt("return",e.$message.error(o.msg));case 13:e.$message.success(o.msg),e.app();case 15:case"end":return t.stop()}}),t)})));return function(e){return t.apply(this,arguments)}}())},editDialog:function(e){var t=this;return Object(n["a"])(regeneratorRuntime.mark((function r(){var a,o;return regeneratorRuntime.wrap((function(r){while(1)switch(r.prev=r.next){case 0:return r.next=2,t.$http.get("app/query/".concat(e));case 2:if(a=r.sent,o=a.data,200===o.code){r.next=6;break}return r.abrupt("return",t.$message.error(o.msg));case 6:t.editForm=o.data,t.editDialogVisible=!0;case 8:case"end":return r.stop()}}),r)})))()},submitEdit:function(){var e=this;this.$refs.editFormRef.validate(function(){var t=Object(n["a"])(regeneratorRuntime.mark((function t(r){var a,o;return regeneratorRuntime.wrap((function(t){while(1)switch(t.prev=t.next){case 0:if(r){t.next=2;break}return t.abrupt("return");case 2:return e.editDialogVisible=!1,e.editForm.name=e.$xss(e.editForm.name),e.editForm.author=e.$xss(e.editForm.author),e.editForm.introduce=e.$xss(e.editForm.introduce),"0"===e.editForm.is_pay&&(e.editForm.money=0),t.next=9,e.$http.put("app/edit",e.editForm);case 9:if(a=t.sent,o=a.data,200===o.code){t.next=13;break}return t.abrupt("return",e.$message.error(o.msg));case 13:e.$message.success(o.msg),e.app();case 15:case"end":return t.stop()}}),t)})));return function(e){return t.apply(this,arguments)}}())},handleSizeChange:function(e){this.queryInfo.per_page=e,this.app()},handleCurrentChange:function(e){this.queryInfo.current_page=e,this.app()},addFormClose:function(){this.$refs.addFormRef.resetFields()},editFormClose:function(){this.$refs.editFormRef.resetFields()},handleAvatarSuccessAddPhoto:function(e,t){if(200!==e.code)return this.$message.error(e.msg);this.addForm.img=e.data[0],this.$message.success(e.msg)},handleAvatarSuccessEditPhoto:function(e,t){if(200!==e.code)return this.$message.error(e.msg);this.editForm.img=e.data[0],this.$message.success(e.msg)},beforeAvatarUpload:function(e){var t="image/jpg"===e.type||"image/jpeg"===e.type||"image/png"===e.type||"image/bmp"===e.type,r=e.size/1024/1024<10;return t?r?t&&r:(this.$message.error("上传图片大小不能超过 10MB!"),!1):(this.$message.error("上传的图片只能是 JPG JPEG PNG BMP格式!"),!1)},handleAvatarSuccessAddFile:function(e,t){if(200!==e.code)return this.$message.error(e.msg);this.addForm.zip=e.data[0],this.$message.success(e.msg)},handleAvatarSuccessEditFile:function(e,t){if(200!==e.code)return this.$message.error(e.msg);this.editForm.zip=e.data[0],this.$message.success(e.msg)},beforeAvatarUploadAddfile:function(e){var t=e.name.substring(e.name.lastIndexOf(".")+1),r=["zip","rar","7z"];if(-1===r.indexOf(t))return this.$message.success("上传的文件只能市zip rar 7z格式！"),!1;var a=e.size/1024/1024<15;return a?void 0:(this.$message.error("上传文件大小不能超过 15MB!"),!1)},$imgAdd:function(e,t){var r=this;return Object(n["a"])(regeneratorRuntime.mark((function a(){var o,n,i,s;return regeneratorRuntime.wrap((function(a){while(1)switch(a.prev=a.next){case 0:if(!r.beforeAvatarUpload(t)){a.next=11;break}return o=new FormData,o.append("image",t),a.next=5,r.$http.post(r.updateUrl,o);case 5:if(n=a.sent,i=n.data,200===i.code){a.next=9;break}return a.abrupt("return",r.$message.error(i.msg));case 9:s=r.$refs.addEditorRef,s.$img2Url(e,i.data[0]);case 11:case"end":return a.stop()}}),a)})))()},$imgEdit:function(e,t){var r=this;return Object(n["a"])(regeneratorRuntime.mark((function a(){var o,n,i,s;return regeneratorRuntime.wrap((function(a){while(1)switch(a.prev=a.next){case 0:if(!r.beforeAvatarUpload(t)){a.next=11;break}return o=new FormData,o.append("image",t),a.next=5,r.$http.post(r.updateUrl,o);case 5:if(n=a.sent,i=n.data,200===i.code){a.next=9;break}return a.abrupt("return",r.$message.error(i.msg));case 9:s=r.$refs.EditEditorRef,s.$img2Url(e,i.data[0]);case 11:case"end":return a.stop()}}),a)})))()}}}),s=i,l=(r("6429"),r("2877")),c=Object(l["a"])(s,a,o,!1,null,"6c4a1913",null);t["default"]=c.exports},6429:function(e,t,r){"use strict";r("098e")},"70b4":function(e,t,r){"use strict";r.r(t);var a=function(){var e=this,t=e.$createElement,r=e._self._c||t;return r("div",[r("el-breadcrumb",{attrs:{"separator-class":"el-icon-arrow-right"}},[r("el-breadcrumb-item",{attrs:{to:{path:"/home"}}},[e._v("首页")]),r("el-breadcrumb-item",[e._v("开发者中心")]),r("el-breadcrumb-item",[e._v("成为开发者")])],1),r("el-card",[r("div",{staticClass:"clearfix",attrs:{slot:"header"},slot:"header"},[r("strong",[e._v("成为开发者")])]),r("el-form",{ref:"developerConfigFormRef",staticClass:"form",attrs:{model:e.developerConfigForm,rules:e.developerConfigFormRules,"label-width":"120px"}},[r("el-form-item",{attrs:{label:"申请条件:"}},[r("el-card",{staticClass:"box-card"},[r("div",{staticClass:"text item"},[e._v(e._s(e.isDeveloperForm.condition))])])],1),r("el-form-item",{attrs:{label:"支付宝账户",prop:"alipay"}},[r("el-input",{attrs:{placeholder:"请输入支付宝账户"},model:{value:e.developerConfigForm.alipay,callback:function(t){e.$set(e.developerConfigForm,"alipay",t)},expression:"developerConfigForm.alipay"}})],1),r("el-form-item",{attrs:{label:"支付宝真实姓名",prop:"alipay_name"}},[r("el-input",{attrs:{placeholder:"请输入支付宝真实姓名"},model:{value:e.developerConfigForm.alipay_name,callback:function(t){e.$set(e.developerConfigForm,"alipay_name",t)},expression:"developerConfigForm.alipay_name"}})],1),r("el-form-item",{attrs:{label:"微信账户",prop:"wxpay"}},[r("el-input",{attrs:{placeholder:"请输入微信账户"},model:{value:e.developerConfigForm.wxpay,callback:function(t){e.$set(e.developerConfigForm,"wxpay",t)},expression:"developerConfigForm.wxpay"}})],1),r("el-form-item",{attrs:{label:"微信真实姓名",prop:"wxpay_name"}},[r("el-input",{attrs:{placeholder:"请输入微信真实姓名"},model:{value:e.developerConfigForm.wxpay_name,callback:function(t){e.$set(e.developerConfigForm,"wxpay_name",t)},expression:"developerConfigForm.wxpay_name"}})],1),r("el-form-item",{attrs:{label:"QQ账户",prop:"qqpay"}},[r("el-input",{attrs:{placeholder:"请输入QQ账户"},model:{value:e.developerConfigForm.qqpay,callback:function(t){e.$set(e.developerConfigForm,"qqpay",t)},expression:"developerConfigForm.qqpay"}})],1),r("el-form-item",{attrs:{label:"QQ名称",prop:"qqpay_name"}},[r("el-input",{attrs:{placeholder:"请输入QQ名称"},model:{value:e.developerConfigForm.qqpay_name,callback:function(t){e.$set(e.developerConfigForm,"qqpay_name",t)},expression:"developerConfigForm.qqpay_name"}})],1),r("el-form-item",[r("el-button",{attrs:{type:"primary",icon:"el-icon-edit"},on:{click:e.developerConfigEdit}},[e._v("提交")])],1)],1)],1)],1)},o=[],n=r("1da1"),i=(r("159b"),r("b64b"),r("96cf"),{data:function(){return{isDeveloperForm:{},developerConfigForm:{},developerConfigFormRules:{alipay:[{required:!0,message:"请输入支付宝账户",trigger:"blur"}],alipay_name:[{required:!0,message:"请输入支付宝真实姓名",trigger:"blur"}],wxpay:[{required:!0,message:"请输入微信账户",trigger:"blur"}],wxpay_name:[{required:!0,message:"请输入微信真实姓名",trigger:"blur"}],qqpay:[{required:!0,message:"请输入QQ账户",trigger:"blur"}],qqpay_name:[{required:!0,message:"请输入QQ名称",trigger:"blur"}]}}},created:function(){this.getDeveloperConfig()},methods:{getDeveloperConfig:function(){var e=this;return Object(n["a"])(regeneratorRuntime.mark((function t(){var r,a;return regeneratorRuntime.wrap((function(t){while(1)switch(t.prev=t.next){case 0:return t.next=2,e.$http.get("Developer/developerConfig");case 2:if(r=t.sent,a=r.data,200===a.code){t.next=6;break}return t.abrupt("return",e.$message.error(a.msg));case 6:e.isDeveloperForm=a.data,e.isForm();case 8:case"end":return t.stop()}}),t)})))()},developerConfigEdit:function(){var e=this;Object.keys(this.developerConfigForm).forEach((function(t){e.developerConfigForm[t]=e.$xss(e.developerConfigForm[t])})),this.$refs.developerConfigFormRef.validate((function(t){t&&e.$confirm("您确认要提交申请?","提示",{confirmButtonText:"确定",cancelButtonText:"取消",type:"warning"}).then(Object(n["a"])(regeneratorRuntime.mark((function t(){var r,a;return regeneratorRuntime.wrap((function(t){while(1)switch(t.prev=t.next){case 0:return t.next=2,e.$http.post("Developer/becomeDeveloper",e.developerConfigForm);case 2:if(r=t.sent,a=r.data,201===a.code){t.next=6;break}return t.abrupt("return",e.$message.error(a.msg));case 6:e.$message.success(a.msg),e.getDeveloperConfig();case 8:case"end":return t.stop()}}),t)})))).catch((function(){e.$message({type:"info",message:"已取消删除"})}))}))},isForm:function(){if("1"===this.isDeveloperForm.is_developer)this.$loading({lock:!0,text:"正在审核中...",spinner:"el-icon-s-order",target:document.querySelector(".form")});else if("3"===this.isDeveloperForm.is_developer){this.customClass="danger-form";this.$loading({lock:!0,text:"已驳回！原因："+this.isDeveloperForm.cause,spinner:"el-icon-s-release",target:document.querySelector(".form")})}else if("2"===this.isDeveloperForm.is_developer)this.$loading({lock:!0,text:"恭喜！您申请成为开发者的请求已经通过审核！",spinner:"el-icon-s-claim",target:document.querySelector(".form")})}},mounted:function(){}}),s=i,l=(r("a556"),r("2877")),c=Object(l["a"])(s,a,o,!1,null,"97a63304",null);t["default"]=c.exports},"96cf":function(e,t,r){var a=function(e){"use strict";var t,r=Object.prototype,a=r.hasOwnProperty,o="function"===typeof Symbol?Symbol:{},n=o.iterator||"@@iterator",i=o.asyncIterator||"@@asyncIterator",s=o.toStringTag||"@@toStringTag";function l(e,t,r){return Object.defineProperty(e,t,{value:r,enumerable:!0,configurable:!0,writable:!0}),e[t]}try{l({},"")}catch(O){l=function(e,t,r){return e[t]=r}}function c(e,t,r,a){var o=t&&t.prototype instanceof h?t:h,n=Object.create(o.prototype),i=new k(a||[]);return n._invoke=C(e,r,i),n}function u(e,t,r){try{return{type:"normal",arg:e.call(t,r)}}catch(O){return{type:"throw",arg:O}}}e.wrap=c;var d="suspendedStart",p="suspendedYield",m="executing",f="completed",g={};function h(){}function v(){}function A(){}var b={};l(b,n,(function(){return this}));var y=Object.getPrototypeOf,w=y&&y(y(D([])));w&&w!==r&&a.call(w,n)&&(b=w);var F=A.prototype=h.prototype=Object.create(b);function x(e){["next","throw","return"].forEach((function(t){l(e,t,(function(e){return this._invoke(t,e)}))}))}function E(e,t){function r(o,n,i,s){var l=u(e[o],e,n);if("throw"!==l.type){var c=l.arg,d=c.value;return d&&"object"===typeof d&&a.call(d,"__await")?t.resolve(d.__await).then((function(e){r("next",e,i,s)}),(function(e){r("throw",e,i,s)})):t.resolve(d).then((function(e){c.value=e,i(c)}),(function(e){return r("throw",e,i,s)}))}s(l.arg)}var o;function n(e,a){function n(){return new t((function(t,o){r(e,a,t,o)}))}return o=o?o.then(n,n):n()}this._invoke=n}function C(e,t,r){var a=d;return function(o,n){if(a===m)throw new Error("Generator is already running");if(a===f){if("throw"===o)throw n;return R()}r.method=o,r.arg=n;while(1){var i=r.delegate;if(i){var s=I(i,r);if(s){if(s===g)continue;return s}}if("next"===r.method)r.sent=r._sent=r.arg;else if("throw"===r.method){if(a===d)throw a=f,r.arg;r.dispatchException(r.arg)}else"return"===r.method&&r.abrupt("return",r.arg);a=m;var l=u(e,t,r);if("normal"===l.type){if(a=r.done?f:p,l.arg===g)continue;return{value:l.arg,done:r.done}}"throw"===l.type&&(a=f,r.method="throw",r.arg=l.arg)}}}function I(e,r){var a=e.iterator[r.method];if(a===t){if(r.delegate=null,"throw"===r.method){if(e.iterator["return"]&&(r.method="return",r.arg=t,I(e,r),"throw"===r.method))return g;r.method="throw",r.arg=new TypeError("The iterator does not provide a 'throw' method")}return g}var o=u(a,e.iterator,r.arg);if("throw"===o.type)return r.method="throw",r.arg=o.arg,r.delegate=null,g;var n=o.arg;return n?n.done?(r[e.resultName]=n.value,r.next=e.nextLoc,"return"!==r.method&&(r.method="next",r.arg=t),r.delegate=null,g):n:(r.method="throw",r.arg=new TypeError("iterator result is not an object"),r.delegate=null,g)}function Q(e){var t={tryLoc:e[0]};1 in e&&(t.catchLoc=e[1]),2 in e&&(t.finallyLoc=e[2],t.afterLoc=e[3]),this.tryEntries.push(t)}function _(e){var t=e.completion||{};t.type="normal",delete t.arg,e.completion=t}function k(e){this.tryEntries=[{tryLoc:"root"}],e.forEach(Q,this),this.reset(!0)}function D(e){if(e){var r=e[n];if(r)return r.call(e);if("function"===typeof e.next)return e;if(!isNaN(e.length)){var o=-1,i=function r(){while(++o<e.length)if(a.call(e,o))return r.value=e[o],r.done=!1,r;return r.value=t,r.done=!0,r};return i.next=i}}return{next:R}}function R(){return{value:t,done:!0}}return v.prototype=A,l(F,"constructor",A),l(A,"constructor",v),v.displayName=l(A,s,"GeneratorFunction"),e.isGeneratorFunction=function(e){var t="function"===typeof e&&e.constructor;return!!t&&(t===v||"GeneratorFunction"===(t.displayName||t.name))},e.mark=function(e){return Object.setPrototypeOf?Object.setPrototypeOf(e,A):(e.__proto__=A,l(e,s,"GeneratorFunction")),e.prototype=Object.create(F),e},e.awrap=function(e){return{__await:e}},x(E.prototype),l(E.prototype,i,(function(){return this})),e.AsyncIterator=E,e.async=function(t,r,a,o,n){void 0===n&&(n=Promise);var i=new E(c(t,r,a,o),n);return e.isGeneratorFunction(r)?i:i.next().then((function(e){return e.done?e.value:i.next()}))},x(F),l(F,s,"Generator"),l(F,n,(function(){return this})),l(F,"toString",(function(){return"[object Generator]"})),e.keys=function(e){var t=[];for(var r in e)t.push(r);return t.reverse(),function r(){while(t.length){var a=t.pop();if(a in e)return r.value=a,r.done=!1,r}return r.done=!0,r}},e.values=D,k.prototype={constructor:k,reset:function(e){if(this.prev=0,this.next=0,this.sent=this._sent=t,this.done=!1,this.delegate=null,this.method="next",this.arg=t,this.tryEntries.forEach(_),!e)for(var r in this)"t"===r.charAt(0)&&a.call(this,r)&&!isNaN(+r.slice(1))&&(this[r]=t)},stop:function(){this.done=!0;var e=this.tryEntries[0],t=e.completion;if("throw"===t.type)throw t.arg;return this.rval},dispatchException:function(e){if(this.done)throw e;var r=this;function o(a,o){return s.type="throw",s.arg=e,r.next=a,o&&(r.method="next",r.arg=t),!!o}for(var n=this.tryEntries.length-1;n>=0;--n){var i=this.tryEntries[n],s=i.completion;if("root"===i.tryLoc)return o("end");if(i.tryLoc<=this.prev){var l=a.call(i,"catchLoc"),c=a.call(i,"finallyLoc");if(l&&c){if(this.prev<i.catchLoc)return o(i.catchLoc,!0);if(this.prev<i.finallyLoc)return o(i.finallyLoc)}else if(l){if(this.prev<i.catchLoc)return o(i.catchLoc,!0)}else{if(!c)throw new Error("try statement without catch or finally");if(this.prev<i.finallyLoc)return o(i.finallyLoc)}}}},abrupt:function(e,t){for(var r=this.tryEntries.length-1;r>=0;--r){var o=this.tryEntries[r];if(o.tryLoc<=this.prev&&a.call(o,"finallyLoc")&&this.prev<o.finallyLoc){var n=o;break}}n&&("break"===e||"continue"===e)&&n.tryLoc<=t&&t<=n.finallyLoc&&(n=null);var i=n?n.completion:{};return i.type=e,i.arg=t,n?(this.method="next",this.next=n.finallyLoc,g):this.complete(i)},complete:function(e,t){if("throw"===e.type)throw e.arg;return"break"===e.type||"continue"===e.type?this.next=e.arg:"return"===e.type?(this.rval=this.arg=e.arg,this.method="return",this.next="end"):"normal"===e.type&&t&&(this.next=t),g},finish:function(e){for(var t=this.tryEntries.length-1;t>=0;--t){var r=this.tryEntries[t];if(r.finallyLoc===e)return this.complete(r.completion,r.afterLoc),_(r),g}},catch:function(e){for(var t=this.tryEntries.length-1;t>=0;--t){var r=this.tryEntries[t];if(r.tryLoc===e){var a=r.completion;if("throw"===a.type){var o=a.arg;_(r)}return o}}throw new Error("illegal catch attempt")},delegateYield:function(e,r,a){return this.delegate={iterator:D(e),resultName:r,nextLoc:a},"next"===this.method&&(this.arg=t),g}},e}(e.exports);try{regeneratorRuntime=a}catch(o){"object"===typeof globalThis?globalThis.regeneratorRuntime=a:Function("r","regeneratorRuntime = r")(a)}},a556:function(e,t,r){"use strict";r("a8e8")},a640:function(e,t,r){"use strict";var a=r("d039");e.exports=function(e,t){var r=[][e];return!!r&&a((function(){r.call(null,t||function(){throw 1},1)}))}},a8e8:function(e,t,r){},b0c0:function(e,t,r){var a=r("83ab"),o=r("9bf2").f,n=Function.prototype,i=n.toString,s=/^\s*function ([^ (]*)/,l="name";a&&!(l in n)&&o(n,l,{configurable:!0,get:function(){try{return i.call(this).match(s)[1]}catch(e){return""}}})},b64b:function(e,t,r){var a=r("23e7"),o=r("7b0b"),n=r("df75"),i=r("d039"),s=i((function(){n(1)}));a({target:"Object",stat:!0,forced:s},{keys:function(e){return n(o(e))}})},c91b:function(e,t){e.exports="data:image/png;base64,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"}}]);
//# sourceMappingURL=applist_developer.389f6d9f.js.map