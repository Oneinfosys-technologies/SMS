import{f as g,G as A,m as V,r as m,o as v,q as D,w as d,d as n,e as s,u as l,s as k,t as B,I as F,a as S,F as E}from"./app-DCfJDSeM.js";const j={class:"grid grid-cols-3 gap-6"},q={class:"col-span-3 sm:col-span-1"},x={class:"col-span-3 sm:col-span-1"},O={class:"col-span-3 sm:col-span-1"},H={class:"col-span-3 sm:col-span-1"},R={class:"col-span-3 sm:col-span-1"},I={class:"col-span-3 sm:col-span-1"},L={class:"col-span-3 sm:col-span-1"},G={class:"col-span-3 sm:col-span-1"},z={class:"col-span-3 sm:col-span-1"},J={class:"col-span-3 sm:col-span-1"},K={class:"col-span-3 sm:col-span-1"},M={class:"col-span-3 sm:col-span-1"},Q={class:"col-span-3 sm:col-span-1"},W={class:"col-span-3 sm:col-span-1"},X={class:"col-span-3 sm:col-span-1"},Y={class:"grid grid-cols-3 gap-6"},Z={class:"col-span-3 sm:col-span-1"},_={class:"col-span-3 sm:col-span-1"},h={class:"col-span-3 sm:col-span-1"},ee={class:"col-span-3 sm:col-span-1"},re={name:"TransportVehicleForm"},oe=Object.assign(re,{setup(y){const u=g(),p={name:"",registrationNumber:"",registrationPlace:"",registrationDate:"",modelNumber:"",make:"",class:"",engineNumber:"",chassisNumber:"",cubicCapacity:"",color:"",fuelType:"",seatingCapacity:"",maxSeatingAllowed:"",fuelCapacity:"",ownerName:"",ownerAddress:"",ownerPhone:"",ownerEmail:""},c="transport/vehicle/",a=A(c),b=V({fuelTypes:[]}),o=V({...p}),f=V({isLoaded:!u.params.uuid}),w=t=>{Object.assign(b,t)},U=t=>{var e,i;Object.assign(p,{...t,registrationDate:(e=t.registrationDate)==null?void 0:e.value,fuelType:(i=t.fuelType)==null?void 0:i.value}),Object.assign(o,F(p)),f.isLoaded=!0};return(t,e)=>{const i=m("BaseInput"),N=m("DatePicker"),$=m("BaseSelect"),P=m("BaseTextarea"),T=m("BaseFieldset"),C=m("FormAction");return v(),D(C,{"pre-requisites":!0,onSetPreRequisites:w,"init-url":c,"init-form":p,form:o,"set-form":U,redirect:"TransportVehicle"},{default:d(()=>[n("div",j,[n("div",q,[s(i,{type:"text",modelValue:o.name,"onUpdate:modelValue":e[0]||(e[0]=r=>o.name=r),name:"name",label:t.$trans("transport.vehicle.props.name"),error:l(a).name,"onUpdate:error":e[1]||(e[1]=r=>l(a).name=r)},null,8,["modelValue","label","error"])]),n("div",x,[s(i,{type:"text",modelValue:o.registrationNumber,"onUpdate:modelValue":e[2]||(e[2]=r=>o.registrationNumber=r),name:"registrationNumber",label:t.$trans("transport.vehicle.props.registration_number"),error:l(a).registrationNumber,"onUpdate:error":e[3]||(e[3]=r=>l(a).registrationNumber=r)},null,8,["modelValue","label","error"])]),n("div",O,[s(i,{type:"text",modelValue:o.registrationPlace,"onUpdate:modelValue":e[4]||(e[4]=r=>o.registrationPlace=r),name:"registrationPlace",label:t.$trans("transport.vehicle.props.registration_place"),error:l(a).registrationPlace,"onUpdate:error":e[5]||(e[5]=r=>l(a).registrationPlace=r)},null,8,["modelValue","label","error"])]),n("div",H,[s(N,{modelValue:o.registrationDate,"onUpdate:modelValue":e[6]||(e[6]=r=>o.registrationDate=r),name:"registrationDate",label:t.$trans("transport.vehicle.props.registration_date"),"no-clear":"",error:l(a).registrationDate,"onUpdate:error":e[7]||(e[7]=r=>l(a).registrationDate=r)},null,8,["modelValue","label","error"])]),n("div",R,[s(i,{type:"text",modelValue:o.engineNumber,"onUpdate:modelValue":e[8]||(e[8]=r=>o.engineNumber=r),name:"engineNumber",label:t.$trans("transport.vehicle.props.engine_number"),error:l(a).engineNumber,"onUpdate:error":e[9]||(e[9]=r=>l(a).engineNumber=r)},null,8,["modelValue","label","error"])]),n("div",I,[s(i,{type:"text",modelValue:o.chassisNumber,"onUpdate:modelValue":e[10]||(e[10]=r=>o.chassisNumber=r),name:"chassisNumber",label:t.$trans("transport.vehicle.props.chassis_number"),error:l(a).chassisNumber,"onUpdate:error":e[11]||(e[11]=r=>l(a).chassisNumber=r)},null,8,["modelValue","label","error"])]),n("div",L,[s(i,{type:"text",modelValue:o.cubicCapacity,"onUpdate:modelValue":e[12]||(e[12]=r=>o.cubicCapacity=r),name:"cubicCapacity",label:t.$trans("transport.vehicle.props.cubic_capacity"),error:l(a).cubicCapacity,"onUpdate:error":e[13]||(e[13]=r=>l(a).cubicCapacity=r)},null,8,["modelValue","label","error"])]),n("div",G,[s(i,{type:"text",modelValue:o.color,"onUpdate:modelValue":e[14]||(e[14]=r=>o.color=r),name:"color",label:t.$trans("transport.vehicle.props.color"),error:l(a).color,"onUpdate:error":e[15]||(e[15]=r=>l(a).color=r)},null,8,["modelValue","label","error"])]),n("div",z,[s(i,{type:"text",modelValue:o.modelNumber,"onUpdate:modelValue":e[16]||(e[16]=r=>o.modelNumber=r),name:"modelNumber",label:t.$trans("transport.vehicle.props.model_number"),error:l(a).modelNumber,"onUpdate:error":e[17]||(e[17]=r=>l(a).modelNumber=r)},null,8,["modelValue","label","error"])]),n("div",J,[s(i,{type:"text",modelValue:o.make,"onUpdate:modelValue":e[18]||(e[18]=r=>o.make=r),name:"make",label:t.$trans("transport.vehicle.props.make"),error:l(a).make,"onUpdate:error":e[19]||(e[19]=r=>l(a).make=r)},null,8,["modelValue","label","error"])]),n("div",K,[s(i,{type:"text",modelValue:o.class,"onUpdate:modelValue":e[20]||(e[20]=r=>o.class=r),name:"class",label:t.$trans("transport.vehicle.props.class"),error:l(a).class,"onUpdate:error":e[21]||(e[21]=r=>l(a).class=r)},null,8,["modelValue","label","error"])]),n("div",M,[s(i,{type:"text",modelValue:o.seatingCapacity,"onUpdate:modelValue":e[22]||(e[22]=r=>o.seatingCapacity=r),name:"seatingCapacity",label:t.$trans("transport.vehicle.props.seating_capacity"),error:l(a).seatingCapacity,"onUpdate:error":e[23]||(e[23]=r=>l(a).seatingCapacity=r)},null,8,["modelValue","label","error"])]),n("div",Q,[s(i,{type:"text",modelValue:o.maxSeatingAllowed,"onUpdate:modelValue":e[24]||(e[24]=r=>o.maxSeatingAllowed=r),name:"maxSeatingAllowed",label:t.$trans("transport.vehicle.props.max_seating_allowed"),error:l(a).maxSeatingAllowed,"onUpdate:error":e[25]||(e[25]=r=>l(a).maxSeatingAllowed=r)},null,8,["modelValue","label","error"])]),n("div",W,[s($,{modelValue:o.fuelType,"onUpdate:modelValue":e[26]||(e[26]=r=>o.fuelType=r),name:"fuelType",label:t.$trans("transport.vehicle.props.fuel_type"),options:b.fuelTypes,error:l(a).fuelType,"onUpdate:error":e[27]||(e[27]=r=>l(a).fuelType=r)},null,8,["modelValue","label","options","error"])]),n("div",X,[s(i,{type:"text",modelValue:o.fuelCapacity,"onUpdate:modelValue":e[28]||(e[28]=r=>o.fuelCapacity=r),name:"fuelCapacity",label:t.$trans("transport.vehicle.props.fuel_capacity"),error:l(a).fuelCapacity,"onUpdate:error":e[29]||(e[29]=r=>l(a).fuelCapacity=r)},null,8,["modelValue","label","error"])])]),s(T,{class:"mt-4"},{legend:d(()=>[k(B(t.$trans("transport.vehicle.owner_info")),1)]),default:d(()=>[n("div",Y,[n("div",Z,[s(i,{type:"text",modelValue:o.ownerName,"onUpdate:modelValue":e[30]||(e[30]=r=>o.ownerName=r),name:"ownerName",label:t.$trans("transport.vehicle.props.owner_name"),error:l(a).ownerName,"onUpdate:error":e[31]||(e[31]=r=>l(a).ownerName=r)},null,8,["modelValue","label","error"])]),n("div",_,[s(i,{type:"text",modelValue:o.ownerPhone,"onUpdate:modelValue":e[32]||(e[32]=r=>o.ownerPhone=r),name:"ownerPhone",label:t.$trans("transport.vehicle.props.owner_phone"),error:l(a).ownerPhone,"onUpdate:error":e[33]||(e[33]=r=>l(a).ownerPhone=r)},null,8,["modelValue","label","error"])]),n("div",h,[s(i,{type:"text",modelValue:o.ownerEmail,"onUpdate:modelValue":e[34]||(e[34]=r=>o.ownerEmail=r),name:"ownerEmail",label:t.$trans("transport.vehicle.props.owner_email"),error:l(a).ownerEmail,"onUpdate:error":e[35]||(e[35]=r=>l(a).ownerEmail=r)},null,8,["modelValue","label","error"])]),n("div",ee,[s(P,{rows:1,modelValue:o.ownerAddress,"onUpdate:modelValue":e[36]||(e[36]=r=>o.ownerAddress=r),name:"ownerAddress",label:t.$trans("transport.vehicle.props.owner_address"),error:l(a).ownerAddress,"onUpdate:error":e[37]||(e[37]=r=>l(a).ownerAddress=r)},null,8,["modelValue","label","error"])])])]),_:1})]),_:1},8,["form"])}}}),ae={name:"TransportVehicleAction"},te=Object.assign(ae,{setup(y){const u=g();return(p,c)=>{const a=m("PageHeaderAction"),b=m("PageHeader"),o=m("ParentTransition");return v(),S(E,null,[s(b,{title:p.$trans(l(u).meta.trans,{attribute:p.$trans(l(u).meta.label)}),navs:[{label:p.$trans("transport.transport"),path:"Transport"},{label:p.$trans("transport.vehicle.vehicle"),path:"TransportVehicleList"}]},{default:d(()=>[s(a,{name:"TransportVehicle",title:p.$trans("transport.vehicle.vehicle"),actions:["list"]},null,8,["title"])]),_:1},8,["title","navs"]),s(o,{appear:"",visibility:!0},{default:d(()=>[s(oe)]),_:1})],64)}}});export{te as default};
