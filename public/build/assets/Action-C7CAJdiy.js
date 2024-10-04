import{f as b,m as g,G as B,r as p,o as V,q as E,w as y,d as i,e as s,u as n,I as P,a as T,F}from"./app-DCfJDSeM.js";const C={class:"grid grid-cols-3 gap-6"},j={class:"col-span-3 sm:col-span-1"},k={class:"col-span-3 sm:col-span-1"},q={class:"col-span-3 sm:col-span-1"},O={class:"col-span-3 sm:col-span-1"},S={class:"col-span-3 sm:col-span-2"},H={class:"col-span-3"},R={name:"EmployeeAttendanceTypeForm"},I=Object.assign(R,{setup(f){b();const d={name:"",code:"",color:"",category:"",alias:"",description:""},l="employee/attendance/type/",m=g({attendanceCategories:[]}),a=B(l),t=g({...d}),c=r=>{Object.assign(m,r)},_=r=>{var e;Object.assign(d,{...r,category:(e=r.category)==null?void 0:e.value}),Object.assign(t,P(d))};return(r,e)=>{const $=p("BaseSelect"),u=p("BaseInput"),v=p("SelectedColorPicker"),U=p("BaseTextarea"),A=p("FormAction");return V(),E(A,{"pre-requisites":!0,onSetPreRequisites:c,"init-url":l,"init-form":d,form:t,"set-form":_,redirect:"EmployeeAttendanceType"},{default:y(()=>[i("div",C,[i("div",j,[s($,{modelValue:t.category,"onUpdate:modelValue":e[0]||(e[0]=o=>t.category=o),name:"category",label:r.$trans("employee.attendance.type.props.category"),options:m.attendanceCategories,error:n(a).category,"onUpdate:error":e[1]||(e[1]=o=>n(a).category=o)},null,8,["modelValue","label","options","error"])]),i("div",k,[s(u,{type:"text",modelValue:t.name,"onUpdate:modelValue":e[2]||(e[2]=o=>t.name=o),name:"name",label:r.$trans("employee.attendance.type.props.name"),error:n(a).name,"onUpdate:error":e[3]||(e[3]=o=>n(a).name=o),autofocus:""},null,8,["modelValue","label","error"])]),i("div",q,[s(u,{type:"text",modelValue:t.alias,"onUpdate:modelValue":e[4]||(e[4]=o=>t.alias=o),name:"alias",label:r.$trans("employee.attendance.type.props.alias"),error:n(a).alias,"onUpdate:error":e[5]||(e[5]=o=>n(a).alias=o)},null,8,["modelValue","label","error"])]),i("div",O,[s(u,{type:"text",modelValue:t.code,"onUpdate:modelValue":e[6]||(e[6]=o=>t.code=o),name:"code",label:r.$trans("employee.attendance.type.props.code"),error:n(a).code,"onUpdate:error":e[7]||(e[7]=o=>n(a).code=o),autofocus:""},null,8,["modelValue","label","error"])]),i("div",S,[s(v,{modelValue:t.color,"onUpdate:modelValue":e[8]||(e[8]=o=>t.color=o),label:r.$trans("general.color"),error:n(a).color,"onUpdate:error":e[9]||(e[9]=o=>n(a).color=o)},null,8,["modelValue","label","error"])]),i("div",H,[s(U,{modelValue:t.description,"onUpdate:modelValue":e[10]||(e[10]=o=>t.description=o),name:"description",label:r.$trans("employee.attendance.type.props.description"),error:n(a).description,"onUpdate:error":e[11]||(e[11]=o=>n(a).description=o)},null,8,["modelValue","label","error"])])])]),_:1},8,["form"])}}}),w={name:"EmployeeAttendanceTypeAction"},D=Object.assign(w,{setup(f){const d=b();return(l,m)=>{const a=p("PageHeaderAction"),t=p("PageHeader"),c=p("ParentTransition");return V(),T(F,null,[s(t,{title:l.$trans(n(d).meta.trans,{attribute:l.$trans(n(d).meta.label)}),navs:[{label:l.$trans("employee.employee"),path:"Employee"},{label:l.$trans("employee.attendance.attendance"),path:"EmployeeAttendance"},{label:l.$trans("employee.attendance.type.type"),path:"EmployeeAttendanceTypeList"}]},{default:y(()=>[s(a,{name:"EmployeeAttendanceType",title:l.$trans("employee.attendance.type.type"),actions:["list"]},null,8,["title"])]),_:1},8,["title","navs"]),s(c,{appear:"",visibility:!0},{default:y(()=>[s(I)]),_:1})],64)}}});export{D as default};
