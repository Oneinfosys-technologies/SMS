import{f as D,i as H,l as O,G as P,m as B,n as M,r as o,o as f,q as V,w as n,d as i,u as j,s as h,t as d,b as L,e as l,a as C,F as N,I as T,v as G,h as I,j as W}from"./app-DCfJDSeM.js";const J={class:"grid grid-cols-3 gap-6"},K={class:"col-span-3 sm:col-span-1"},Q={class:"col-span-3 sm:col-span-1"},X={__name:"Filter",props:{initUrl:{type:String,default:""}},emits:["hide","cancel"],setup(A,{emit:b}){const m=D(),c=H(),_=b,g=A,S={batch:"",subject:""},u=O(!1),v=P(g.initUrl),r=B({...S}),e=B({selectedBatch:null,subjects:[]}),p=B({batch:"",subject:"",isLoaded:!(m.query.batch&&m.query.subject)}),F=()=>{e.selectedBatch=null,_("cancel")},q=async s=>{if(!(s!=null&&s.uuid)){r.batch="",r.subject="",e.subjects=[];return}r.batch=(s==null?void 0:s.uuid)||"",e.subjects=[],r.subject="",u.value=!0,await c.dispatch("academic/batch/listSubjects",{uuid:(s==null?void 0:s.uuid)||""}).then(a=>{e.subjects=a.filter(y=>y.isElective==!0),u.value=!1}).catch(a=>{u.value=!1})};return M(async()=>{p.batch=m.query.batch,r.batch=m.query.batch,p.subject=m.query.subject,r.subject=m.query.subject,p.isLoaded=!0}),(s,a)=>{const y=o("BaseSelectSearch"),k=o("BaseSelect"),U=o("FilterForm");return f(),V(U,{"init-form":S,form:r,onCancel:F,onHide:a[4]||(a[4]=t=>_("hide"))},{default:n(()=>[i("div",J,[i("div",K,[p.isLoaded?(f(),V(y,{key:0,name:"batch",label:s.$trans("global.select",{attribute:s.$trans("academic.batch.batch")}),modelValue:e.selectedBatch,"onUpdate:modelValue":a[0]||(a[0]=t=>e.selectedBatch=t),error:j(v).batch,"onUpdate:error":a[1]||(a[1]=t=>j(v).batch=t),"value-prop":"uuid","object-prop":!0,"init-search":p.batch,"search-key":"course_batch","search-action":"academic/batch/list",onChange:q},{selectedOption:n(t=>[h(d(t.value.course.name)+" "+d(t.value.name),1)]),listOption:n(t=>[h(d(t.option.course.nameWithTerm)+" "+d(t.option.name),1)]),_:1},8,["label","modelValue","error","init-search"])):L("",!0)]),i("div",Q,[l(k,{modelValue:r.subject,"onUpdate:modelValue":a[2]||(a[2]=t=>r.subject=t),name:"subject",label:s.$trans("academic.subject.subject"),"label-prop":"name","value-prop":"uuid",options:e.subjects,error:j(v).subject,"onUpdate:error":a[3]||(a[3]=t=>j(v).subject=t)},null,8,["modelValue","label","options","error"])])])]),_:1},8,["form"])}}},Y={class:"p-2"},Z={class:"divide-y divide-gray-200 dark:divide-gray-700"},x={class:"grid grid-cols-4 gap-6 px-4 py-2"},ee={class:"col-span-4 sm:col-span-1"},te={class:"col-span-4 sm:col-span-1"},se={class:"col-span-4 sm:col-span-1"},ae={class:"col-span-4 sm:col-span-1"},ne={name:"StudentRollNumber"},ce=Object.assign(ne,{setup(A){const b=D();I();const m=H();W("emitter");const c={batch:"",subject:"",students:[]},_="student/subject/",g=O(!1);B({});const S=P(_),u=B({...c});B({});const v=()=>{c.batch="",c.subject="",c.students=[],Object.assign(u,T(c))},r=async()=>{!b.query.batch||!b.query.subject||(g.value=!0,await m.dispatch(_+"fetch",{params:b.query}).then(e=>{g.value=!1,c.batch=b.query.batch,c.subject=b.query.subject,c.students=e,Object.assign(u,T(c))}).catch(e=>{g.value=!1}))};return M(async()=>{await r()}),(e,p)=>{const F=o("PageHeaderAction"),q=o("PageHeader"),s=o("ParentTransition"),a=o("BaseAlert"),y=o("BaseLabel"),k=o("TextMuted"),U=o("BaseDataView"),t=o("BaseSwitch"),R=o("FormAction"),z=o("BaseCard");return f(),C(N,null,[l(q,{title:e.$trans(j(b).meta.label),navs:[{label:e.$trans("student.student"),path:"Student"}]},{default:n(()=>[l(F)]),_:1},8,["title","navs"]),l(s,{appear:"",visibility:!0},{default:n(()=>[l(X,{onAfterFilter:r,onCancel:v,"init-url":_})]),_:1}),l(z,{"no-padding":"","no-content-padding":"","is-loading":g.value},{title:n(()=>[h(d(e.$trans("global.assign",{attribute:e.$trans("student.student")})),1)]),action:n(()=>p[0]||(p[0]=[])),default:n(()=>[i("div",Y,[u.students.length==0?(f(),V(a,{key:0,size:"xs",design:"error"},{default:n(()=>[h(d(e.$trans("general.errors.record_not_found")),1)]),_:1})):L("",!0)]),u.students.length?(f(),V(R,{key:0,"no-card":"","button-padding":"","keep-adding":!1,"stay-on":!0,"init-url":_,action:"store","init-form":c,form:u},{default:n(()=>[i("div",Z,[i("div",x,[i("div",ee,[l(y,null,{default:n(()=>[h(d(e.$trans("student.student")),1)]),_:1})]),i("div",te,[l(y,null,{default:n(()=>[h(d(e.$trans("student.subject.subject")),1)]),_:1})])]),(f(!0),C(N,null,G(u.students,($,E)=>(f(),C("div",{class:"grid grid-cols-4 gap-6 px-4 py-2",key:$.uuid},[i("div",se,[l(U,null,{default:n(()=>[h(d($.name)+" ",1),l(k,{block:""},{default:n(()=>[h(d($.codeNumber),1)]),_:2},1024)]),_:2},1024)]),i("div",ae,[l(t,{modelValue:$.hasElectiveSubject,"onUpdate:modelValue":w=>$.hasElectiveSubject=w,name:`student.${E}.hasElectiveSubject`,label:e.$trans("general.opt_in"),error:j(S)[`students.${E}.hasElectiveSubject`],"onUpdate:error":w=>j(S)[`students.${E}.hasElectiveSubject`]=w},null,8,["modelValue","onUpdate:modelValue","name","label","error","onUpdate:error"])])]))),128))])]),_:1},8,["form"])):L("",!0)]),_:1},8,["is-loading"])],64)}}});export{ce as default};
