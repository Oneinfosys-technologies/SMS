import{m as U,G as L,c as T,n as A,I as D,a as c,q as v,u as r,b as f,e as l,w as V,F as C,r as u,o as i,d,s as H,t as G,y as M,f as z,h as J,j as K}from"./app-DCfJDSeM.js";const Q={class:"grid grid-cols-3 gap-6"},W={class:"col-span-3 sm:col-span-1"},X={class:"grid grid-cols-3 gap-6"},Y={class:"col-span-3 sm:col-span-1"},Z={key:0,class:"col-span-3 sm:col-span-1"},h={key:1,class:"col-span-3 sm:col-span-1"},_={class:"col-span-3 sm:col-span-1"},ee={key:2,class:"col-span-3 sm:col-span-1"},oe={class:"col-span-3 sm:col-span-1"},se={class:"col-span-3 sm:col-span-1"},ae={name:"EmployeeEditLogin"},te=Object.assign(ae,{props:{employee:{type:Object,default(){return{}}}},setup(p){const B=z(),S=J(),E=K("emitter"),P=p,g={username:"",email:"",forceChangePassword:!1,password:"",passwordConfirmation:"",roles:[]},w="employee/",j="user/",$=U({}),t=L(w),y=T(()=>P.employee.contact.user),n=U({isValidated:!1,existingUser:null}),a=U({...g}),q=s=>{Object.assign($,s)},F=s=>{n.isValidated=!0,n.existingUser=s||null,a.username=s==null?void 0:s.username},R=()=>{E.emit("employeeUpdated"),S.push({name:"EmployeeShowLogin",params:{uuid:P.employee.uuid}})};return A(async()=>{var s,e,b;n.isValidated=!!y.value,n.existingUser=y.value,Object.assign(g,{username:(s=y.value)==null?void 0:s.username,email:(e=y.value)==null?void 0:e.email,roles:((b=y.value)==null?void 0:b.roles.map(m=>m.uuid))||[]}),Object.assign(a,D(g))}),(s,e)=>{const b=u("PageHeader"),m=u("BaseInput"),O=u("BaseButton"),k=u("FormAction"),N=u("BaseSelect"),x=u("BaseSwitch"),I=u("ParentTransition");return i(),c(C,null,[p.employee.uuid?(i(),v(b,{key:0,title:s.$trans(r(B).meta.trans,{attribute:s.$trans(r(B).meta.label)}),navs:[{label:s.$trans("employee.employee"),path:"Employee"},{label:p.employee.contact.name,path:{name:"EmployeeShow",params:{uuid:p.employee.uuid}}}]},null,8,["title","navs"])):f("",!0),l(I,{appear:"",visibility:!0},{default:V(()=>[p.employee.uuid?(i(),c(C,{key:0},[n.isValidated?(i(),v(k,{key:1,"no-data-fetch":"","pre-requisites":!0,"pre-requisite-url":j,onSetPreRequisites:q,"init-url":w,action:n.existingUser?"updateUser":"createUser","init-form":g,form:a,"after-submit":R},{default:V(()=>[d("div",X,[d("div",Y,[l(m,{readonly:"",disabled:"",type:"text",modelValue:a.email,"onUpdate:modelValue":e[2]||(e[2]=o=>a.email=o),name:"email",label:s.$trans("contact.login.props.email"),error:r(t).email,"onUpdate:error":e[3]||(e[3]=o=>r(t).email=o)},null,8,["modelValue","label","error"])]),n.existingUser?(i(),c("div",Z,[l(m,{readonly:"",disabled:"",type:"text",modelValue:a.username,"onUpdate:modelValue":e[4]||(e[4]=o=>a.username=o),name:"username",label:s.$trans("contact.login.props.username"),error:r(t).username,"onUpdate:error":e[5]||(e[5]=o=>r(t).username=o)},null,8,["modelValue","label","error"])])):f("",!0),n.existingUser?f("",!0):(i(),c("div",h,[l(m,{type:"text",modelValue:a.username,"onUpdate:modelValue":e[6]||(e[6]=o=>a.username=o),name:"username",label:s.$trans("contact.login.props.username"),error:r(t).username,"onUpdate:error":e[7]||(e[7]=o=>r(t).username=o)},null,8,["modelValue","label","error"])])),d("div",_,[l(N,{modelValue:a.roles,"onUpdate:modelValue":e[8]||(e[8]=o=>a.roles=o),name:"roles",label:s.$trans("contact.login.props.role"),options:$.roles,multiple:"","label-prop":"label","value-prop":"uuid",error:r(t).roles,"onUpdate:error":e[9]||(e[9]=o=>r(t).roles=o)},null,8,["modelValue","label","options","error"])]),n.existingUser&&r(M)("user:edit")?(i(),c("div",ee,[l(x,{vertical:"",modelValue:a.forceChangePassword,"onUpdate:modelValue":e[10]||(e[10]=o=>a.forceChangePassword=o),name:"forceChangePassword",label:s.$trans("global.change",{attribute:s.$trans("contact.login.props.password")}),error:r(t).forceChangePassword,"onUpdate:error":e[11]||(e[11]=o=>r(t).forceChangePassword=o)},null,8,["modelValue","label","error"])])):f("",!0),!n.existingUser||a.forceChangePassword?(i(),c(C,{key:3},[d("div",oe,[l(m,{type:"password",modelValue:a.password,"onUpdate:modelValue":e[12]||(e[12]=o=>a.password=o),name:"password",label:s.$trans("contact.login.props.password"),error:r(t).password,"onUpdate:error":e[13]||(e[13]=o=>r(t).password=o)},null,8,["modelValue","label","error"])]),d("div",se,[l(m,{type:"password",modelValue:a.passwordConfirmation,"onUpdate:modelValue":e[14]||(e[14]=o=>a.passwordConfirmation=o),name:"passwordConfirmation",label:s.$trans("contact.login.props.password_confirmation"),error:r(t).passwordConfirmation,"onUpdate:error":e[15]||(e[15]=o=>r(t).passwordConfirmation=o)},null,8,["modelValue","label","error"])])],64)):f("",!0)])]),_:1},8,["action","form"])):(i(),v(k,{key:0,"no-action-button":"","no-data-fetch":"","init-url":w,action:"confirmUser","init-form":g,form:a,"after-submit":F,"stay-on":"",redirect:{name:"EmployeeShowLogin",params:{uuid:p.employee.uuid}}},{default:V(()=>[d("div",Q,[d("div",W,[l(m,{type:"text",modelValue:a.email,"onUpdate:modelValue":e[0]||(e[0]=o=>a.email=o),name:"email",label:s.$trans("contact.login.props.email"),error:r(t).email,"onUpdate:error":e[1]||(e[1]=o=>r(t).email=o)},null,8,["modelValue","label","error"])])]),l(O,{class:"mt-4",type:"submit"},{default:V(()=>[H(G(s.$trans("general.validate")),1)]),_:1})]),_:1},8,["form","redirect"]))],64)):f("",!0)]),_:1})],64)}}});export{te as default};
