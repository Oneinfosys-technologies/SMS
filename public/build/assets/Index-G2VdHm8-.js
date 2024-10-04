import{m as w,r as n,o as f,q as C,w as e,d as $,e as a,G as j,l as I,n as T,s as i,t as d,a as B,u,b as U,f as N,i as O,F as M,A as S,v as z,h as q,j as G,z as J}from"./app-DCfJDSeM.js";import{_ as K}from"./ModuleDropdown-BYIhYXQ6.js";import{d as Q}from"./vuedraggable.umd-DO5pvILy.js";const W={class:"grid grid-cols-3 gap-6"},X={class:"col-span-3 sm:col-span-1"},Y={class:"col-span-3 sm:col-span-1"},Z={class:"col-span-3 sm:col-span-1"},x={__name:"Filter",emits:["hide"],setup(H,{emit:_}){const y=_,v={name:"",code:"",alias:""},m=w({...v});return(c,l)=>{const r=n("BaseInput"),b=n("FilterForm");return f(),C(b,{"init-form":v,form:m,onHide:l[3]||(l[3]=t=>y("hide"))},{default:e(()=>[$("div",W,[$("div",X,[a(r,{type:"text",modelValue:m.name,"onUpdate:modelValue":l[0]||(l[0]=t=>m.name=t),name:"name",label:c.$trans("employee.payroll.pay_head.props.name")},null,8,["modelValue","label"])]),$("div",Y,[a(r,{type:"text",modelValue:m.code,"onUpdate:modelValue":l[1]||(l[1]=t=>m.code=t),name:"code",label:c.$trans("employee.payroll.pay_head.props.code")},null,8,["modelValue","label"])]),$("div",Z,[a(r,{type:"text",modelValue:m.alias,"onUpdate:modelValue":l[2]||(l[2]=t=>m.alias=t),name:"alias",label:c.$trans("employee.payroll.pay_head.props.alias")},null,8,["modelValue","label"])])])]),_:1},8,["form"])}}},ee={key:0},ae={class:"flex border rounded-xl px-4 py-2"},te={key:1},le={key:2,class:"mt-4 flex justify-end"},oe={name:"EmployeePayrollPayHeadReorder"},se=Object.assign(oe,{props:{visibility:{type:Boolean,default:!1}},emits:["close","refresh"],setup(H,{emit:_}){N();const y=O(),v=_,m={payHeads:[]};j("employee/payroll/payHead/");const l=I(!1),r=w({payHeads:[]});w({...m});const b=async()=>{l.value=!0,await y.dispatch("employee/payroll/payHead/list",{params:{all:!0}}).then(p=>{l.value=!1,r.payHeads=p}).catch(p=>{l.value=!1})},t=async()=>{l.value=!0,await y.dispatch("employee/payroll/payHead/reorder",{data:{payHeads:r.payHeads}}).then(p=>{l.value=!1,v("refresh"),v("close")}).catch(p=>{l.value=!1})},o=()=>{v("close")};return T(()=>{b()}),(p,P)=>{const F=n("BaseLabel"),k=n("BaseAlert"),g=n("BaseButton"),h=n("BaseModal");return f(),C(h,{show:H.visibility,onClose:o},{title:e(()=>[i(d(p.$trans("global.reorder",{attribute:p.$trans("employee.payroll.pay_head.pay_head")})),1)]),default:e(()=>[r.payHeads.length?(f(),B("div",ee,[a(u(Q),{class:"space-y-2",list:r.payHeads,"item-key":"uuid"},{item:e(({element:V,index:D})=>[$("div",ae,[P[0]||(P[0]=$("i",{class:"fas fa-arrows mr-2 cursor-pointer"},null,-1)),a(F,null,{default:e(()=>[i(d(V.name),1)]),_:2},1024)])]),_:1},8,["list"])])):(f(),B("div",te,[a(k,{design:"info",size:"xs"},{default:e(()=>[i(d(p.$trans("general.errors.record_not_found")),1)]),_:1})])),r.payHeads.length?(f(),B("div",le,[a(g,{onClick:t},{default:e(()=>[i(d(p.$trans("general.reorder")),1)]),_:1})])):U("",!0)]),_:1},8,["show"])}}}),ne={name:"EmployeePayrollPayHeadList"},pe=Object.assign(ne,{setup(H){const _=q(),y=G("emitter");let v=["create","filter"];const m="employee/payroll/payHead/",c=I(!1),l=I(!1),r=w({}),b=t=>{Object.assign(r,t)};return(t,o)=>{const p=n("BaseButton"),P=n("PageHeaderAction"),F=n("PageHeader"),k=n("ParentTransition"),g=n("DataCell"),h=n("FloatingMenuItem"),V=n("FloatingMenu"),D=n("DataRow"),R=n("DataTable"),A=n("ListItem"),L=J("tooltip");return f(),B(M,null,[a(A,{"init-url":m,onSetItems:b},{header:e(()=>[a(F,{title:t.$trans("employee.payroll.pay_head.pay_head"),navs:[{label:t.$trans("employee.employee"),path:"Employee"},{label:t.$trans("employee.payroll.payroll"),path:"EmployeePayroll"}]},{default:e(()=>[a(P,{url:"employee/payroll/pay-heads/",name:"EmployeePayrollPayHead",title:t.$trans("employee.payroll.pay_head.pay_head"),actions:u(v),"dropdown-actions":["print","pdf","excel"],onToggleFilter:o[1]||(o[1]=s=>c.value=!c.value)},{moduleOption:e(()=>[a(K)]),default:e(()=>[S((f(),C(p,{design:"white",onClick:o[0]||(o[0]=s=>l.value=!l.value)},{default:e(()=>o[8]||(o[8]=[$("i",{class:"fas fa-arrows-up-down-left-right"},null,-1)])),_:1})),[[L,t.$trans("global.reorder",{attribute:t.$trans("employee.payroll.pay_head.pay_head")})]])]),_:1},8,["title","actions"])]),_:1},8,["title","navs"])]),filter:e(()=>[a(k,{appear:"",visibility:c.value},{default:e(()=>[a(x,{onRefresh:o[2]||(o[2]=s=>u(y).emit("listItems")),onHide:o[3]||(o[3]=s=>c.value=!1)})]),_:1},8,["visibility"])]),default:e(()=>[a(k,{appear:"",visibility:!0},{default:e(()=>[a(R,{header:r.headers,meta:r.meta,module:"employee.payroll.pay_head",onRefresh:o[5]||(o[5]=s=>u(y).emit("listItems"))},{actionButton:e(()=>[a(p,{onClick:o[4]||(o[4]=s=>u(_).push({name:"EmployeePayrollPayHeadCreate"}))},{default:e(()=>[i(d(t.$trans("global.add",{attribute:t.$trans("employee.payroll.pay_head.pay_head")})),1)]),_:1})]),default:e(()=>[(f(!0),B(M,null,z(r.data,s=>(f(),C(D,{key:s.uuid},{default:e(()=>[a(g,{name:"name"},{default:e(()=>[i(d(s.name),1)]),_:2},1024),a(g,{name:"code"},{default:e(()=>[i(d(s.code),1)]),_:2},1024),a(g,{name:"alias"},{default:e(()=>[i(d(s.alias),1)]),_:2},1024),a(g,{name:"category"},{default:e(()=>[i(d(s.category.label),1)]),_:2},1024),a(g,{name:"createdAt"},{default:e(()=>[i(d(s.createdAt.formatted),1)]),_:2},1024),a(g,{name:"action"},{default:e(()=>[a(V,null,{default:e(()=>[a(h,{icon:"fas fa-arrow-circle-right",onClick:E=>u(_).push({name:"EmployeePayrollPayHeadShow",params:{uuid:s.uuid}})},{default:e(()=>[i(d(t.$trans("general.show")),1)]),_:2},1032,["onClick"]),a(h,{icon:"fas fa-edit",onClick:E=>u(_).push({name:"EmployeePayrollPayHeadEdit",params:{uuid:s.uuid}})},{default:e(()=>[i(d(t.$trans("general.edit")),1)]),_:2},1032,["onClick"]),a(h,{icon:"fas fa-copy",onClick:E=>u(_).push({name:"EmployeePayrollPayHeadDuplicate",params:{uuid:s.uuid}})},{default:e(()=>[i(d(t.$trans("general.duplicate")),1)]),_:2},1032,["onClick"]),a(h,{icon:"fas fa-trash",onClick:E=>u(y).emit("deleteItem",{uuid:s.uuid})},{default:e(()=>[i(d(t.$trans("general.delete")),1)]),_:2},1032,["onClick"])]),_:2},1024)]),_:2},1024)]),_:2},1024))),128))]),_:1},8,["header","meta"])]),_:1})]),_:1}),a(se,{visibility:l.value,onClose:o[6]||(o[6]=s=>l.value=!1),onRefresh:o[7]||(o[7]=s=>u(y).emit("listItems"))},null,8,["visibility"])],64)}}});export{pe as default};
