import{f as w,j as D,m as f,G as H,n as F,r as l,o as b,q as k,w as _,d as y,e as o,y as A,l as h,a as V,u as m,F as j,i as B}from"./app-DCfJDSeM.js";const S={class:"grid grid-cols-3 gap-6"},U={class:"col-span-3 sm:col-span-1"},L={__name:"Filter",props:{initUrl:{type:String,default:""}},emits:["hide"],setup(v,{emit:d}){w(),D("moment");const u=d,p=v,n={date:""},r=f({...n});H(p.initUrl);const s=f({isLoaded:!0});return F(async()=>{s.isLoaded=!0}),(i,t)=>{const c=l("DatePicker"),e=l("FilterForm");return b(),k(e,{"init-form":n,multiple:[],form:r,onHide:t[1]||(t[1]=a=>u("hide"))},{default:_(()=>[y("div",S,[y("div",U,[o(c,{modelValue:r.date,"onUpdate:modelValue":t[0]||(t[0]=a=>r.date=a),name:"date",as:"date",label:i.$trans("general.date")},null,8,["modelValue","label"])])])]),_:1},8,["form"])}}},T={name:"ResourceReportDateWiseStudentDiary"},C=Object.assign(T,{setup(v){const d=w(),u=B();let p=["filter"],n=[];A("resource:report")&&(n=["print","pdf","excel"]);const r="resource/report/",s=h(!0),i=h(!1),t=f({headers:[],data:[],meta:{total:0}}),c=async()=>{i.value=!0,await u.dispatch(r+"fetchReport",{name:"date-wise-student-diary",params:d.query}).then(e=>{i.value=!1,Object.assign(t,e)}).catch(e=>{i.value=!1})};return F(async()=>{await c()}),(e,a)=>{const R=l("PageHeaderAction"),$=l("PageHeader"),g=l("ParentTransition");return b(),V(j,null,[o($,{title:e.$trans(m(d).meta.label),navs:[{label:e.$trans("resource.resource"),path:"Resource"},{label:e.$trans("resource.report.report"),path:"ResourceReport"}]},{default:_(()=>[o(R,{url:"resource/reports/date-wise-student-diary/",name:"ResourceReportDateWiseStudentDiary",title:e.$trans("resource.report.date_wise_student_diary.date_wise_student_diary"),actions:m(p),"dropdown-actions":m(n),headers:t.headers,onToggleFilter:a[0]||(a[0]=P=>s.value=!s.value)},null,8,["title","actions","dropdown-actions","headers"])]),_:1},8,["title","navs"]),o(g,{appear:"",visibility:s.value},{default:_(()=>[o(L,{onAfterFilter:c,"init-url":r,onHide:a[1]||(a[1]=P=>s.value=!1)})]),_:1},8,["visibility"]),o(g,{appear:"",visibility:!0})],64)}}});export{C as default};
