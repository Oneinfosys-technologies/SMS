import{f as g,G as B,m as f,r as i,o as u,q as $,w as b,d as m,e as o,u as s,a as h,b as V,I as G,F as I}from"./app-DCfJDSeM.js";const F={class:"grid grid-cols-3 gap-6"},k={class:"col-span-3 sm:col-span-1"},R={class:"col-span-3 sm:col-span-1"},C={key:0,class:"col-span-3 sm:col-span-1"},M={key:0,class:"mt-6 grid grid-cols-3 gap-6"},j={class:"col-span-3 sm:col-span-1"},q={class:"col-span-3 sm:col-span-1"},A={class:"col-span-3 sm:col-span-1"},O={class:"col-span-3 sm:col-span-1"},H={class:"col-span-3 sm:col-span-1"},T={class:"col-span-3"},E={name:"FinancePaymentMethodForm"},L=Object.assign(E,{setup(v){const p=g(),l={name:"",hasInstrumentDate:!1,hasInstrumentNumber:!1,hasClearingDate:!1,hasBankDetail:!1,hasReferenceNumber:!1,isPaymentGateway:!1,paymentGatewayName:"",description:""},c="finance/paymentMethod/",t=B(c),y=f({}),n=f({...l}),N=f({isLoaded:!p.params.uuid}),P=r=>{Object.assign(y,r)},w=r=>{Object.assign(l,{...r}),Object.assign(n,G(l)),N.isLoaded=!0};return(r,e)=>{const _=i("BaseInput"),d=i("BaseSwitch"),D=i("BaseTextarea"),U=i("FormAction");return u(),$(U,{"pre-requisites":!1,onSetPreRequisites:P,"init-url":c,"init-form":l,form:n,"set-form":w,redirect:"FinancePaymentMethod"},{default:b(()=>[m("div",F,[m("div",k,[o(_,{type:"text",modelValue:n.name,"onUpdate:modelValue":e[0]||(e[0]=a=>n.name=a),name:"name",label:r.$trans("finance.payment_method.props.name"),error:s(t).name,"onUpdate:error":e[1]||(e[1]=a=>s(t).name=a)},null,8,["modelValue","label","error"])]),m("div",R,[o(d,{vertical:"",modelValue:n.isPaymentGateway,"onUpdate:modelValue":e[2]||(e[2]=a=>n.isPaymentGateway=a),name:"isPaymentGateway",label:r.$trans("finance.payment_method.props.is_payment_gateway"),error:s(t).isPaymentGateway,"onUpdate:error":e[3]||(e[3]=a=>s(t).isPaymentGateway=a)},null,8,["modelValue","label","error"])]),n.isPaymentGateway?(u(),h("div",C,[o(_,{type:"text",modelValue:n.paymentGatewayName,"onUpdate:modelValue":e[4]||(e[4]=a=>n.paymentGatewayName=a),name:"paymentGatewayName",label:r.$trans("finance.payment_method.props.payment_gateway_name"),error:s(t).paymentGatewayName,"onUpdate:error":e[5]||(e[5]=a=>s(t).paymentGatewayName=a)},null,8,["modelValue","label","error"])])):V("",!0)]),n.isPaymentGateway?V("",!0):(u(),h("div",M,[m("div",j,[o(d,{vertical:"",modelValue:n.hasInstrumentNumber,"onUpdate:modelValue":e[6]||(e[6]=a=>n.hasInstrumentNumber=a),name:"hasInstrumentNumber",label:r.$trans("finance.payment_method.props.has_instrument_number"),error:s(t).hasInstrumentNumber,"onUpdate:error":e[7]||(e[7]=a=>s(t).hasInstrumentNumber=a)},null,8,["modelValue","label","error"])]),m("div",q,[o(d,{vertical:"",modelValue:n.hasInstrumentDate,"onUpdate:modelValue":e[8]||(e[8]=a=>n.hasInstrumentDate=a),name:"hasInstrumentDate",label:r.$trans("finance.payment_method.props.has_instrument_date"),error:s(t).hasInstrumentDate,"onUpdate:error":e[9]||(e[9]=a=>s(t).hasInstrumentDate=a)},null,8,["modelValue","label","error"])]),m("div",A,[o(d,{vertical:"",modelValue:n.hasClearingDate,"onUpdate:modelValue":e[10]||(e[10]=a=>n.hasClearingDate=a),name:"hasClearingDate",label:r.$trans("finance.payment_method.props.has_clearing_date"),error:s(t).hasClearingDate,"onUpdate:error":e[11]||(e[11]=a=>s(t).hasClearingDate=a)},null,8,["modelValue","label","error"])]),m("div",O,[o(d,{vertical:"",modelValue:n.hasBankDetail,"onUpdate:modelValue":e[12]||(e[12]=a=>n.hasBankDetail=a),name:"hasBankDetail",label:r.$trans("finance.payment_method.props.has_bank_detail"),error:s(t).hasBankDetail,"onUpdate:error":e[13]||(e[13]=a=>s(t).hasBankDetail=a)},null,8,["modelValue","label","error"])]),m("div",H,[o(d,{vertical:"",modelValue:n.hasReferenceNumber,"onUpdate:modelValue":e[14]||(e[14]=a=>n.hasReferenceNumber=a),name:"hasReferenceNumber",label:r.$trans("finance.payment_method.props.has_reference_number"),error:s(t).hasReferenceNumber,"onUpdate:error":e[15]||(e[15]=a=>s(t).hasReferenceNumber=a)},null,8,["modelValue","label","error"])]),m("div",T,[o(D,{modelValue:n.description,"onUpdate:modelValue":e[16]||(e[16]=a=>n.description=a),name:"description",label:r.$trans("finance.payment_method.props.description"),error:s(t).description,"onUpdate:error":e[17]||(e[17]=a=>s(t).description=a)},null,8,["modelValue","label","error"])])]))]),_:1},8,["form"])}}}),S={name:"FinancePaymentMethodAction"},J=Object.assign(S,{setup(v){const p=g();return(l,c)=>{const t=i("PageHeaderAction"),y=i("PageHeader"),n=i("ParentTransition");return u(),h(I,null,[o(y,{title:l.$trans(s(p).meta.trans,{attribute:l.$trans(s(p).meta.label)}),navs:[{label:l.$trans("finance.finance"),path:"Finance"},{label:l.$trans("finance.payment_method.payment_method"),path:"FinancePaymentMethodList"}]},{default:b(()=>[o(t,{name:"FinancePaymentMethod",title:l.$trans("finance.payment_method.payment_method"),actions:["list"]},null,8,["title"])]),_:1},8,["title","navs"]),o(n,{appear:"",visibility:!0},{default:b(()=>[o(L)]),_:1})],64)}}});export{J as default};
