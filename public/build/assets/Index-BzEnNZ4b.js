import{f as j,j as O,m as b,G as N,n as k,r,o as p,q,w as o,d as v,e as s,y as E,l as V,a as F,u as D,F as R,v as U,s as $,t as P,h as W,i as G}from"./app-DCfJDSeM.js";const z={class:"grid grid-cols-3 gap-6"},I={class:"col-span-3 sm:col-span-1"},J={class:"col-span-3 sm:col-span-1"},K={class:"col-span-3 sm:col-span-1"},Q={__name:"Filter",props:{initUrl:{type:String,default:""},preRequisites:{type:Object,default(){return{}}}},emits:["hide"],setup(f,{emit:m}){j(),O("moment");const _=m,h=f,u={startDate:"",endDate:"",period:"",ledgers:[]},t=b({...u});N(h.initUrl);const i=b({isLoaded:!0});return k(async()=>{i.isLoaded=!0}),(a,n)=>{const l=r("DatePicker"),g=r("BaseSelect"),c=r("FilterForm");return p(),q(c,{"init-form":u,multiple:[],form:t,onHide:n[4]||(n[4]=e=>_("hide"))},{default:o(()=>[v("div",z,[v("div",I,[s(l,{start:t.startDate,"onUpdate:start":n[0]||(n[0]=e=>t.startDate=e),end:t.endDate,"onUpdate:end":n[1]||(n[1]=e=>t.endDate=e),name:"dateBetween",as:"range",label:a.$trans("general.date_between")},null,8,["start","end","label"])]),v("div",J,[s(g,{name:"period",label:a.$trans("global.select",{attribute:a.$trans("academic.period.period")}),modelValue:t.period,"onUpdate:modelValue":n[2]||(n[2]=e=>t.period=e),"label-prop":"name","value-prop":"uuid",options:f.preRequisites.periods},null,8,["label","modelValue","options"])]),v("div",K,[s(g,{multiple:"",name:"ledgers",label:a.$trans("global.select",{attribute:a.$trans("finance.ledger.ledger")}),modelValue:t.ledgers,"onUpdate:modelValue":n[3]||(n[3]=e=>t.ledgers=e),"label-prop":"name","value-prop":"uuid",options:f.preRequisites.ledgers},null,8,["label","modelValue","options"])])])]),_:1},8,["form"])}}},X={name:"FinanceReportPaymentMethodWiseFeePayment"},Z=Object.assign(X,{setup(f){const m=j();W();const _=G();let h=["filter"],u=[];E("finance:export")&&(u=["print","pdf","excel"]);const t="finance/report/",i=V(!1),a=V(!1),n=b({periods:[],ledgers:[]}),l=b({headers:[],data:[],meta:{total:0}}),g=async()=>{a.value=!0,await _.dispatch(t+"preRequisite",{name:"payment-method-wise-fee-payment",params:m.query}).then(e=>{a.value=!1,Object.assign(n,e)}).catch(e=>{a.value=!1})},c=async()=>{a.value=!0,await _.dispatch(t+"fetchReport",{name:"payment-method-wise-fee-payment",params:m.query}).then(e=>{a.value=!1,Object.assign(l,e)}).catch(e=>{a.value=!1})};return k(async()=>{await g(),await c()}),(e,y)=>{const C=r("PageHeaderAction"),H=r("PageHeader"),B=r("ParentTransition"),w=r("DataCell"),T=r("DataRow"),A=r("DataTable"),S=r("BaseCard");return p(),F(R,null,[s(H,{title:e.$trans(D(m).meta.label),navs:[{label:e.$trans("finance.finance"),path:"Finance"},{label:e.$trans("finance.report.report"),path:"FinanceReport"}]},{default:o(()=>[s(C,{url:"finance/reports/payment-method-wise-fee-payment/",name:"FinanceReportPaymentMethodWiseFeePayment",title:e.$trans("finance.report.payment_method_wise_fee_payment.payment_method_wise_fee_payment"),actions:D(h),"dropdown-actions":D(u),headers:l.headers,onToggleFilter:y[0]||(y[0]=d=>i.value=!i.value)},null,8,["title","actions","dropdown-actions","headers"])]),_:1},8,["title","navs"]),s(B,{appear:"",visibility:i.value},{default:o(()=>[s(Q,{onAfterFilter:c,"init-url":t,"pre-requisites":n,onHide:y[1]||(y[1]=d=>i.value=!1)},null,8,["pre-requisites"])]),_:1},8,["visibility"]),s(B,{appear:"",visibility:!0},{default:o(()=>[s(S,{"no-padding":"","no-content-padding":"","is-loading":a.value},{default:o(()=>[s(A,{header:l.headers,footer:l.footers,meta:l.meta,module:"finance.report.payment_method_wise_fee_payment",onRefresh:c},{default:o(()=>[(p(!0),F(R,null,U(l.data,d=>(p(),q(T,{key:d.uuid},{default:o(()=>[s(w,{name:"date"},{default:o(()=>[$(P(d.date.formatted),1)]),_:2},1024),(p(!0),F(R,null,U(d.paymentMethods,(L,M)=>(p(),q(w,{name:M},{default:o(()=>[$(P(L.formatted),1)]),_:2},1032,["name"]))),256)),s(w,{name:"total"},{default:o(()=>[$(P(d.total.formatted),1)]),_:2},1024)]),_:2},1024))),128))]),_:1},8,["header","footer","meta"])]),_:1},8,["is-loading"])]),_:1})],64)}}});export{Z as default};
