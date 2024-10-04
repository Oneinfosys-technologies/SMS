import{f as L,h as j,i as P,m as w,n as O,r as s,o as d,q as f,w as e,e as t,s as l,t as i,d as I,l as S,u as y,a as H,v as T,b as h,F as U,j as E}from"./app-DCfJDSeM.js";const z={class:"grid grid-cols-3 gap-6"},G={class:"col-span-3 sm:col-span-1"},J={__name:"Filter",props:{preRequisites:{type:Object,default(){return{}}}},emits:["refresh","hide"],setup(R,{emit:C}){const r=L(),$=j();P();const u=C,c={name:""},m=w({...c}),_=()=>{Object.assign(m,c),n(),u("hide")};O(async()=>{Object.assign(m,{search:r.query.search})});const n=async()=>{await $.push({name:r.name,query:{...r.query,...m}}),u("refresh")};return(o,p)=>{const b=s("BaseInput"),v=s("BaseButton"),g=s("BaseCard");return d(),f(g,null,{footer:e(()=>[t(v,{design:"error",class:"mr-4",onClick:_},{default:e(()=>[l(i(o.$trans("general.cancel")),1)]),_:1}),t(v,{onClick:n},{default:e(()=>[l(i(o.$trans("general.filter")),1)]),_:1})]),default:e(()=>[I("div",z,[I("div",G,[t(b,{type:"text",modelValue:m.name,"onUpdate:modelValue":p[0]||(p[0]=B=>m.name=B),name:"name",label:o.$trans("config.push_notification.template.props.name")},null,8,["modelValue","label"])])])]),_:1})}}},K={name:"ConfigPushNotificationTemplateList"},W=Object.assign(K,{setup(R){const C=j();P();const r=E("emitter"),$="config/pushNotificationTemplate/",u=S(!1),c=w({}),m=n=>{Object.assign(c,n)},_=async n=>{r.emit("actionItem",{uuid:n.uuid,action:"toggleStatus",confirmation:!0})};return(n,o)=>{const p=s("PageHeaderAction"),b=s("ParentTransition"),v=s("BaseBadge"),g=s("DataCell"),B=s("TextMuted"),k=s("FloatingMenuItem"),D=s("FloatingMenu"),M=s("DataRow"),V=s("DataTable"),q=s("ListItem"),A=s("ConfigPage");return d(),f(A,{"no-background":""},{action:e(()=>[t(p,{name:"ConfigPushNotificationTemplate",title:n.$trans("config.push_notification.template.template"),actions:["filter"],onToggleFilter:o[0]||(o[0]=a=>u.value=!u.value)},null,8,["title"])]),default:e(()=>[t(q,{class:"sm:-mt-4","init-url":$,onSetItems:m},{filter:e(()=>[t(b,{appear:"",visibility:u.value},{default:e(()=>[t(J,{onRefresh:o[1]||(o[1]=a=>y(r).emit("listItems")),onHide:o[2]||(o[2]=a=>u.value=!1)})]),_:1},8,["visibility"])]),default:e(()=>[t(b,{appear:"",visibility:!0},{default:e(()=>[t(V,{header:c.headers,meta:c.meta,module:"config.push_notification.template",onRefresh:o[3]||(o[3]=a=>y(r).emit("listItems"))},{actionButton:e(()=>o[4]||(o[4]=[])),default:e(()=>[(d(!0),H(U,null,T(c.data,a=>(d(),f(M,{key:a.uuid},{default:e(()=>[t(g,{name:"name"},{default:e(()=>[l(i(a.name)+" ",1),a.enabledAt.value?h("",!0):(d(),f(v,{key:0,design:"danger"},{default:e(()=>[l(i(n.$trans("config.template.statuses.disabled")),1)]),_:1}))]),_:2},1024),t(g,{name:"subject"},{default:e(()=>[l(i(a.subject)+" ",1),t(B,{block:""},{default:e(()=>[l(i(a.content),1)]),_:2},1024)]),_:2},1024),t(g,{name:"action"},{default:e(()=>[t(D,null,{default:e(()=>[a.enabledAt.value?(d(),f(k,{key:0,icon:"far fa-times-circle",onClick:F=>_(a)},{default:e(()=>[l(i(n.$trans("global.disable",{attribute:n.$trans("config.template.template")})),1)]),_:2},1032,["onClick"])):h("",!0),a.enabledAt.value?h("",!0):(d(),f(k,{key:1,icon:"far fa-check-circle",onClick:F=>_(a)},{default:e(()=>[l(i(n.$trans("global.enable",{attribute:n.$trans("config.template.template")})),1)]),_:2},1032,["onClick"])),t(k,{icon:"fas fa-arrow-circle-right",as:"link",target:"_blank",href:`/app/config/push-notification-template/${a.uuid}`},{default:e(()=>[l(i(n.$trans("general.show")),1)]),_:2},1032,["href"]),t(k,{icon:"fas fa-edit",onClick:F=>y(C).push({name:"ConfigPushNotificationTemplateEdit",params:{uuid:a.uuid}})},{default:e(()=>[l(i(n.$trans("general.edit")),1)]),_:2},1032,["onClick"])]),_:2},1024)]),_:2},1024)]),_:2},1024))),128))]),_:1},8,["header","meta"])]),_:1})]),_:1})]),_:1})}}});export{W as default};
