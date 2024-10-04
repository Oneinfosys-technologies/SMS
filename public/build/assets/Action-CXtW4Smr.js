import{f as L,E as A,G as T,m as v,n as H,r as l,o as y,q as I,w as u,d as r,e as d,u as n,a as U,F as P,v as N,s as B,t as V,L as z,I as G}from"./app-DCfJDSeM.js";const M={class:"grid grid-cols-3 gap-6"},J={class:"col-span-2 sm:col-span-1"},K={class:"col-span-3 sm:col-span-1"},Q={class:"flex items-end justify-between space-x-4"},W={class:"flex-1"},X={class:"col-span-3 sm:col-span-1"},Y={class:"col-span-3 sm:col-span-1"},Z={class:"mt-4"},x={class:"mt-4"},ee={class:"col"},oe={name:"LibraryBookAdditionForm"},te=Object.assign(oe,{setup(w){const m=L(),c={date:"",copies:[],description:""},$={uuid:A(),book:"",number:"",condition:""},b="library/bookAddition/",s=T(b),_=v({conditions:[]}),F=v({isLoaded:!m.params.uuid}),i=v({...c}),S=e=>{Object.assign(_,e)},h=()=>{i.copies.push({...$,uuid:A()})},j=async e=>{await z()&&(i.copies.length==1?i.copies=[$]:i.copies.splice(e,1))},O=e=>{var k;let a=[];e.copies.forEach(f=>{var g;a.push({...f,condition:(g=f.condition)==null?void 0:g.uuid})}),Object.assign(c,{...e,copies:a,date:(k=e.date)==null?void 0:k.value}),Object.assign(i,G(c)),F.isLoaded=!0};return H(async()=>{m.params.uuid||h()}),(e,a)=>{const k=l("DatePicker"),f=l("BaseButton"),g=l("BaseSelectSearch"),D=l("BaseInput"),R=l("BaseSelect"),q=l("BaseBadge"),C=l("BaseTextarea"),E=l("FormAction");return y(),I(E,{"pre-requisites":!0,onSetPreRequisites:S,"init-url":b,"init-form":c,form:i,"set-form":O,redirect:"LibraryBookAddition"},{default:u(()=>[r("div",M,[r("div",J,[d(k,{modelValue:i.date,"onUpdate:modelValue":a[0]||(a[0]=t=>i.date=t),name:"date",label:e.$trans("library.book_addition.props.date"),"no-clear":"",error:n(s).date,"onUpdate:error":a[1]||(a[1]=t=>n(s).date=t)},null,8,["modelValue","label","error"])])]),(y(!0),U(P,null,N(i.copies,(t,p)=>(y(),U("div",{class:"mt-4 grid grid-cols-3 gap-4",key:t.uuid},[r("div",K,[r("div",Q,[r("div",null,[d(f,{size:"xs",design:"danger",onClick:o=>j(p)},{default:u(()=>a[4]||(a[4]=[r("i",{class:"fas fa-trash"},null,-1)])),_:2},1032,["onClick"])]),r("div",W,[d(g,{name:`copies.${p}.book`,label:e.$trans("global.select",{attribute:e.$trans("library.book.book")}),modelValue:t.book,"onUpdate:modelValue":o=>t.book=o,error:n(s)[`copies.${p}.book`],"onUpdate:error":o=>n(s)[`copies.${p}.book`]=o,"label-prop":"title","value-prop":"uuid","object-prop":!0,"search-key":"title","search-action":"library/book/list"},{selectedOption:u(o=>[B(V(o.value.title),1)]),listOption:u(o=>[B(V(o.option.title),1)]),_:2},1032,["name","label","modelValue","onUpdate:modelValue","error","onUpdate:error"])])])]),r("div",X,[d(D,{type:"text",modelValue:t.number,"onUpdate:modelValue":o=>t.number=o,name:`copies.${p}.number`,label:e.$trans("library.book_addition.props.number"),error:n(s)[`copies.${p}.number`],"onUpdate:error":o=>n(s)[`copies.${p}.number`]=o},null,8,["modelValue","onUpdate:modelValue","name","label","error","onUpdate:error"])]),r("div",Y,[d(R,{name:`copies.${p}.detail`,label:e.$trans("global.select",{attribute:e.$trans("library.book_condition.book_condition")}),modelValue:t.condition,"onUpdate:modelValue":o=>t.condition=o,error:n(s)[`copies.${p}.condition`],"onUpdate:error":o=>n(s)[`copies.${p}.condition`]=o,"value-prop":"uuid","label-prop":"name",options:_.conditions},null,8,["name","label","modelValue","onUpdate:modelValue","error","onUpdate:error","options"])])]))),128)),r("div",Z,[d(q,{design:"primary",onClick:h,class:"cursor-pointer"},{default:u(()=>[B(V(e.$trans("global.add",{attribute:e.$trans("general.row")})),1)]),_:1})]),r("div",x,[r("div",ee,[d(C,{rows:1,modelValue:i.remarks,"onUpdate:modelValue":a[2]||(a[2]=t=>i.remarks=t),name:"remarks",label:e.$trans("library.book_addition.props.remarks"),error:n(s).remarks,"onUpdate:error":a[3]||(a[3]=t=>n(s).remarks=t)},null,8,["modelValue","label","error"])])])]),_:1},8,["form"])}}}),ae={name:"LibraryBookAdditionAction"},re=Object.assign(ae,{setup(w){const m=L();return(c,$)=>{const b=l("PageHeaderAction"),s=l("PageHeader"),_=l("ParentTransition");return y(),U(P,null,[d(s,{title:c.$trans(n(m).meta.trans,{attribute:c.$trans(n(m).meta.label)}),navs:[{label:c.$trans("library.library"),path:"Library"},{label:c.$trans("library.book_addition.book_addition"),path:"LibraryBookAdditionList"}]},{default:u(()=>[d(b,{name:"LibraryBookAddition",title:c.$trans("library.book_addition.book_addition"),actions:["list"]},null,8,["title"])]),_:1},8,["title","navs"]),d(_,{appear:"",visibility:!0},{default:u(()=>[d(te)]),_:1})],64)}}});export{re as default};
