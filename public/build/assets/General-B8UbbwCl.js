import{G as T,m as p,a as R,e,u as t,w as d,F as v,r as a,o as D,d as u,f as F,j as I}from"./app-DCfJDSeM.js";const P={class:"grid grid-cols-3 gap-4"},B={class:"col-span-3 sm:col-span-1"},V={name:"TransportConfigGeneral"},x=Object.assign(V,{setup(j){const m=F(),o=I("$trans"),i="config/",l=T(i);p({});const c={showTransportRouteInDashboard:!1,type:"transport"},s=p({...c}),_=f=>{};return(f,r)=>{const h=a("PageHeader"),b=a("BaseSwitch"),g=a("FormAction"),w=a("ParentTransition");return D(),R(v,null,[e(h,{title:t(o)(t(m).meta.label),navs:[{label:t(o)("transport.transport"),path:"Transport"}]},null,8,["title","navs"]),e(w,{appear:"",visibility:!0},{default:d(()=>[e(g,{"pre-requisites":!1,onSetPreRequisites:_,"init-url":i,"data-fetch":"transport","init-form":c,form:s,action:"store","stay-on":"",redirect:"Transport"},{default:d(()=>[u("div",P,[u("div",B,[e(b,{vertical:"",modelValue:s.showTransportRouteInDashboard,"onUpdate:modelValue":r[0]||(r[0]=n=>s.showTransportRouteInDashboard=n),name:"showTransportRouteInDashboard",label:t(o)("global.show",{attribute:t(o)("transport.config.props.route_in_dashboard")}),error:t(l).showTransportRouteInDashboard,"onUpdate:error":r[1]||(r[1]=n=>t(l).showTransportRouteInDashboard=n)},null,8,["modelValue","label","error"])])])]),_:1},8,["form"])]),_:1})],64)}}});export{x as default};
