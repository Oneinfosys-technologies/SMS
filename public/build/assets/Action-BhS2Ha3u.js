import{G as g,m as b,r,o as _,q as v,w as c,d as u,e as o,u as s,a as F,F as T,f as $}from"./app-DCfJDSeM.js";const B={class:"grid grid-cols-2 gap-6"},P={class:"col-span-2"},A={name:"TeamForm"},V=Object.assign(A,{setup(p){const a={name:""},e="team/",m=g(e),n=b({...a});return(i,t)=>{const d=r("BaseInput"),f=r("FormAction");return _(),v(f,{"init-url":e,"init-form":a,form:n,redirect:"Team"},{default:c(()=>[u("div",B,[u("div",P,[o(d,{type:"text",modelValue:n.name,"onUpdate:modelValue":t[0]||(t[0]=l=>n.name=l),name:"name",label:i.$trans("team.props.name"),error:s(m).name,"onUpdate:error":t[1]||(t[1]=l=>s(m).name=l),autofocus:""},null,8,["modelValue","label","error"])])])]),_:1},8,["form"])}}}),H={name:"TeamAction"},E=Object.assign(H,{setup(p){const a=$();return(e,m)=>{const n=r("PageHeaderAction"),i=r("PageHeader"),t=r("ParentTransition");return _(),F(T,null,[o(i,{title:e.$trans(s(a).meta.trans,{attribute:e.$trans(s(a).meta.label)}),navs:[{label:e.$trans("team.team"),path:"TeamList"}]},{default:c(()=>[o(n,{name:"Team",title:e.$trans("team.team"),actions:["list"]},null,8,["title"])]),_:1},8,["title","navs"]),o(t,{appear:"",visibility:!0},{default:c(()=>[o(V)]),_:1})],64)}}});export{E as default};
