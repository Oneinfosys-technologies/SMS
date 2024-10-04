import{f as U,m as w,n as P,r as u,o as d,q as f,w as e,d as q,e as o,s as i,t as n,a as S,b as v,y as W,l as T,u as x,v as z,F as G,h as J,i as K,j as Q}from"./app-DCfJDSeM.js";import"./lodash-BwwPoz7C.js";const X={class:"grid grid-cols-3 gap-6"},Y={class:"col-span-3 sm:col-span-1"},Z={key:0},ee={key:0},te={class:"col-span-3 sm:col-span-1"},ae={class:"col-span-3 sm:col-span-1"},se={__name:"Filter",props:{preRequisites:{type:Object,default(){return{}}}},emits:["hide"],setup(A,{emit:F}){const c=U(),R=F,k={batches:[],exams:[],attempt:""},p=w({...k}),b=w({batches:[],exams:[],isLoaded:!(c.query.batches||c.query.exams)});return P(async()=>{b.batches=c.query.batches?c.query.batches.split(","):[],b.exams=c.query.exams?c.query.exams.split(","):[],b.isLoaded=!0}),(r,m)=>{const _=u("BaseSelect"),V=u("BaseSelectSearch"),B=u("FilterForm");return d(),f(B,{"init-form":k,form:p,multiple:["exams","batches"],onHide:m[3]||(m[3]=s=>R("hide"))},{default:e(()=>[q("div",X,[q("div",Y,[o(_,{multiple:"",modelValue:p.exams,"onUpdate:modelValue":m[0]||(m[0]=s=>p.exams=s),name:"exams",label:r.$trans("global.select",{attribute:r.$trans("exam.exam")}),"track-by":["name"],"value-prop":"uuid",options:A.preRequisites.exams},{selectedOption:e(s=>{var $,h;return[i(n(s.value.name)+" ",1),s.value.term?(d(),S("span",Z,"("+n(((h=($=s.value.term)==null?void 0:$.division)==null?void 0:h.name)||r.$trans("general.all"))+")",1)):v("",!0)]}),listOption:e(s=>{var $,h;return[i(n(s.option.name)+" ",1),s.option.term?(d(),S("span",ee,"("+n(((h=($=s.option.term)==null?void 0:$.division)==null?void 0:h.name)||r.$trans("general.all"))+")",1)):v("",!0)]}),_:1},8,["modelValue","label","options"])]),q("div",te,[b.isLoaded?(d(),f(_,{key:0,modelValue:p.attempt,"onUpdate:modelValue":m[1]||(m[1]=s=>p.attempt=s),name:"attempt",label:r.$trans("exam.schedule.props.attempt"),options:A.preRequisites.attempts},null,8,["modelValue","label","options"])):v("",!0)]),q("div",ae,[b.isLoaded?(d(),f(V,{key:0,multiple:"",name:"batches",label:r.$trans("global.select",{attribute:r.$trans("academic.batch.batch")}),modelValue:p.batches,"onUpdate:modelValue":m[2]||(m[2]=s=>p.batches=s),"value-prop":"uuid","init-search":b.batches,"search-key":"course_batch","search-action":"academic/batch/list"},{selectedOption:e(s=>[i(n(s.value.course.name)+" "+n(s.value.name),1)]),listOption:e(s=>[i(n(s.option.course.nameWithTerm)+" "+n(s.option.name),1)]),_:1},8,["label","modelValue","init-search"])):v("",!0)])])]),_:1},8,["form"])}}},ne={name:"ExamFormList"},le=Object.assign(ne,{setup(A){J();const F=K(),c=Q("emitter");let R=["filter"],k=[];W("form:export")&&(k=["print","pdf","excel"]);const p="exam/form/",b=w({attempts:[],exams:[]}),r=T(!1),m=T(!1),_=w({}),V=a=>{Object.assign(_,a)},B=a=>{Object.assign(b,a)},s=async a=>{r.value=!0,await F.dispatch(p+"printExamForm",{uuid:a}).then(l=>{r.value=!1,window.open("/print").document.write(l)}).catch(l=>{r.value=!1})},$=async a=>{r.value=!0,await F.dispatch(p+"printAdmitCard",{uuid:a}).then(l=>{r.value=!1,window.open("/print").document.write(l)}).catch(l=>{r.value=!1})},h=async(a,l)=>{c.emit("actionItem",{uuid:a.uuid,action:"updateStatus",data:{status:l},confirmation:!0})};return(a,l)=>{const D=u("PageHeaderAction"),O=u("PageHeader"),I=u("ParentTransition"),M=u("BaseBadge"),L=u("TextMuted"),g=u("DataCell"),y=u("FloatingMenuItem"),N=u("FloatingMenu"),j=u("DataRow"),E=u("DataTable"),H=u("ListItem");return d(),f(H,{"init-url":p,"pre-requisites":!0,onSetPreRequisites:B,onSetItems:V},{header:e(()=>[o(O,{title:a.$trans("exam.form.form"),navs:[{label:a.$trans("exam.exam"),path:"Exam"}]},{default:e(()=>[o(D,{url:"exam/forms/",name:"ExamForm",title:a.$trans("exam.form.form"),actions:x(R),"dropdown-actions":x(k),onToggleFilter:l[0]||(l[0]=t=>m.value=!m.value)},null,8,["title","actions","dropdown-actions"])]),_:1},8,["title","navs"])]),filter:e(()=>[o(I,{appear:"",visibility:m.value},{default:e(()=>[o(se,{onRefresh:l[1]||(l[1]=t=>x(c).emit("listItems")),"pre-requisites":b,onHide:l[2]||(l[2]=t=>m.value=!1)},null,8,["pre-requisites"])]),_:1},8,["visibility"])]),default:e(()=>[o(I,{appear:"",visibility:!0},{default:e(()=>[o(E,{header:_.headers,meta:_.meta,module:"exam.form",onRefresh:l[3]||(l[3]=t=>x(c).emit("listItems"))},{default:e(()=>[(d(!0),S(G,null,z(_.data,t=>(d(),f(j,{key:t.uuid},{default:e(()=>[o(g,{name:"schedule"},{default:e(()=>[i(n(t.schedule.exam.name)+" ",1),t.schedule.isReassessment?(d(),f(M,{key:0},{default:e(()=>[i(n(a.$trans("exam.schedule.reassessment")+" ("+t.schedule.attempt.label+")"),1)]),_:2},1024)):v("",!0),t.schedule.exam.term?(d(),f(L,{key:1,block:""},{default:e(()=>[i(n(t.schedule.exam.term.name),1)]),_:2},1024)):v("",!0)]),_:2},1024),o(g,{name:"student"},{default:e(()=>[i(n(t.student.name)+" ",1),o(L,{block:""},{default:e(()=>[i(n(t.student.codeNumber),1)]),_:2},1024)]),_:2},1024),o(g,{name:"batch"},{default:e(()=>[i(n(t.student.courseName+" "+t.student.batchName),1)]),_:2},1024),o(g,{name:"submittedAt"},{default:e(()=>[i(n(t.submittedAt.formatted||"-"),1)]),_:2},1024),o(g,{name:"approvedAt"},{default:e(()=>[i(n(t.approvedAt.formatted||"-"),1)]),_:2},1024),o(g,{name:"action"},{default:e(()=>[o(N,null,{default:e(()=>[o(y,{icon:"fas fa-print",onClick:C=>s(t.uuid)},{default:e(()=>[i(n(a.$trans("global.print",{attribute:a.$trans("exam.schedule.form")})),1)]),_:2},1032,["onClick"]),t.approvedAt.value?(d(),f(y,{key:0,icon:"fas fa-id-card",onClick:C=>$(t.uuid)},{default:e(()=>[i(n(a.$trans("global.print",{attribute:a.$trans("exam.admit_card.admit_card")})),1)]),_:2},1032,["onClick"])):v("",!0),t.approvedAt.value?(d(),f(y,{key:1,icon:"fas fa-times-circle",onClick:C=>h(t,"disapprove")},{default:e(()=>[i(n(a.$trans("global.disapprove",{attribute:a.$trans("exam.form.form")})),1)]),_:2},1032,["onClick"])):v("",!0),t.approvedAt.value?v("",!0):(d(),f(y,{key:2,icon:"fas fa-check-circle",onClick:C=>h(t,"approve")},{default:e(()=>[i(n(a.$trans("global.approve",{attribute:a.$trans("exam.form.form")})),1)]),_:2},1032,["onClick"])),o(y,{icon:"fas fa-trash",onClick:C=>x(c).emit("deleteItem",{uuid:t.uuid})},{default:e(()=>[i(n(a.$trans("general.delete")),1)]),_:2},1032,["onClick"])]),_:2},1024)]),_:2},1024)]),_:2},1024))),128))]),_:1},8,["header","meta"])]),_:1})]),_:1})}}});export{le as default};
