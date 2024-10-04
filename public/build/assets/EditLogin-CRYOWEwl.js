import{m as v,G as L,c as T,n as A,I as D,a as c,q as y,u as a,b as f,e as l,w,F as C,r as d,o as i,d as m,s as H,t as G,y as M,f as z,h as J,j as K}from"./app-DCfJDSeM.js";const Q={class:"grid grid-cols-3 gap-6"},W={class:"col-span-3 sm:col-span-1"},X={class:"grid grid-cols-3 gap-6"},Y={class:"col-span-3 sm:col-span-1"},Z={key:0,class:"col-span-3 sm:col-span-1"},h={key:1,class:"col-span-3 sm:col-span-1"},_={class:"col-span-3 sm:col-span-1"},ee={key:2,class:"col-span-3 sm:col-span-1"},se={class:"col-span-3 sm:col-span-1"},te={class:"col-span-3 sm:col-span-1"},oe={name:"StudentEditLogin"},re=Object.assign(oe,{props:{student:{type:Object,default(){return{}}}},setup(p){const S=z(),k=J(),j=K("emitter"),B=p,g={username:"",email:"",forceChangePassword:!1,password:"",passwordConfirmation:"",roles:[]},U="student/",q="user/",P=v({}),r=L(U),b=T(()=>{var t,e;return(e=(t=B.student)==null?void 0:t.contact)==null?void 0:e.user}),n=v({isValidated:!1,existingUser:null}),o=v({...g}),F=t=>{Object.assign(P,t)},R=t=>{n.isValidated=!0,n.existingUser=t||null,o.username=t==null?void 0:t.username},O=()=>{j.emit("studentUpdated"),k.push({name:"StudentShowLogin",params:{uuid:B.student.uuid}})};return A(async()=>{var t,e,V;n.isValidated=!!b.value,n.existingUser=b.value,Object.assign(g,{username:(t=b.value)==null?void 0:t.username,email:(e=b.value)==null?void 0:e.email,roles:((V=b.value)==null?void 0:V.roles.map(u=>u.uuid))||[]}),Object.assign(o,D(g))}),(t,e)=>{const V=d("PageHeader"),u=d("BaseInput"),E=d("BaseButton"),$=d("FormAction"),N=d("BaseSelect"),x=d("BaseSwitch"),I=d("ParentTransition");return i(),c(C,null,[p.student.uuid?(i(),y(V,{key:0,title:t.$trans(a(S).meta.trans,{attribute:t.$trans(a(S).meta.label)}),navs:[{label:t.$trans("student.student"),path:"Student"},{label:p.student.contact.name,path:{name:"StudentShow",params:{uuid:p.student.uuid}}}]},null,8,["title","navs"])):f("",!0),l(I,{appear:"",visibility:!0},{default:w(()=>[p.student.uuid?(i(),c(C,{key:0},[n.isValidated?(i(),y($,{key:1,"no-data-fetch":"","pre-requisites":!0,"pre-requisite-url":q,onSetPreRequisites:F,"init-url":U,action:n.existingUser?"updateUser":"createUser","init-form":g,form:o,"after-submit":O},{default:w(()=>[m("div",X,[m("div",Y,[l(u,{readonly:"",disabled:"",type:"text",modelValue:o.email,"onUpdate:modelValue":e[2]||(e[2]=s=>o.email=s),name:"email",label:t.$trans("contact.login.props.email"),error:a(r).email,"onUpdate:error":e[3]||(e[3]=s=>a(r).email=s)},null,8,["modelValue","label","error"])]),n.existingUser?(i(),c("div",Z,[l(u,{readonly:"",disabled:"",type:"text",modelValue:o.username,"onUpdate:modelValue":e[4]||(e[4]=s=>o.username=s),name:"username",label:t.$trans("contact.login.props.username"),error:a(r).username,"onUpdate:error":e[5]||(e[5]=s=>a(r).username=s)},null,8,["modelValue","label","error"])])):f("",!0),n.existingUser?f("",!0):(i(),c("div",h,[l(u,{type:"text",modelValue:o.username,"onUpdate:modelValue":e[6]||(e[6]=s=>o.username=s),name:"username",label:t.$trans("contact.login.props.username"),error:a(r).username,"onUpdate:error":e[7]||(e[7]=s=>a(r).username=s)},null,8,["modelValue","label","error"])])),m("div",_,[l(N,{modelValue:o.roles,"onUpdate:modelValue":e[8]||(e[8]=s=>o.roles=s),name:"roles",label:t.$trans("contact.login.props.role"),options:P.roles,multiple:"","label-prop":"label","value-prop":"uuid",error:a(r).roles,"onUpdate:error":e[9]||(e[9]=s=>a(r).roles=s)},null,8,["modelValue","label","options","error"])]),n.existingUser&&a(M)("user:edit")?(i(),c("div",ee,[l(x,{vertical:"",modelValue:o.forceChangePassword,"onUpdate:modelValue":e[10]||(e[10]=s=>o.forceChangePassword=s),name:"forceChangePassword",label:t.$trans("global.change",{attribute:t.$trans("contact.login.props.password")}),error:a(r).forceChangePassword,"onUpdate:error":e[11]||(e[11]=s=>a(r).forceChangePassword=s)},null,8,["modelValue","label","error"])])):f("",!0),!n.existingUser||o.forceChangePassword?(i(),c(C,{key:3},[m("div",se,[l(u,{type:"password",modelValue:o.password,"onUpdate:modelValue":e[12]||(e[12]=s=>o.password=s),name:"password",label:t.$trans("contact.login.props.password"),error:a(r).password,"onUpdate:error":e[13]||(e[13]=s=>a(r).password=s)},null,8,["modelValue","label","error"])]),m("div",te,[l(u,{type:"password",modelValue:o.passwordConfirmation,"onUpdate:modelValue":e[14]||(e[14]=s=>o.passwordConfirmation=s),name:"passwordConfirmation",label:t.$trans("contact.login.props.password_confirmation"),error:a(r).passwordConfirmation,"onUpdate:error":e[15]||(e[15]=s=>a(r).passwordConfirmation=s)},null,8,["modelValue","label","error"])])],64)):f("",!0)])]),_:1},8,["action","form"])):(i(),y($,{key:0,"no-action-button":"","no-data-fetch":"","init-url":U,action:"confirmUser","init-form":g,form:o,"after-submit":R,"stay-on":"",redirect:{name:"StudentShowLogin",params:{uuid:p.student.uuid}}},{default:w(()=>[m("div",Q,[m("div",W,[l(u,{type:"text",modelValue:o.email,"onUpdate:modelValue":e[0]||(e[0]=s=>o.email=s),name:"email",label:t.$trans("contact.login.props.email"),error:a(r).email,"onUpdate:error":e[1]||(e[1]=s=>a(r).email=s)},null,8,["modelValue","label","error"])])]),l(E,{class:"mt-4",type:"submit"},{default:w(()=>[H(G(t.$trans("general.validate")),1)]),_:1})]),_:1},8,["form","redirect"]))],64)):f("",!0)]),_:1})],64)}}});export{re as default};
