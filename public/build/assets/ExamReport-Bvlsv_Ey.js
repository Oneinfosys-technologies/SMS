import{l as E,m as H,n as A,a as i,q as l,w as e,u as n,b as f,e as c,F as m,r as t,o as a,s as h,t as g,v as b,f as N,i as V,j as F}from"./app-DCfJDSeM.js";const L={name:"StudentShowExamReport"},q=Object.assign(L,{props:{student:{type:Object,default(){return{}}}},setup(o){const v=N(),w=V(),s=F("$trans"),y=o,x="student/",u=E(!1),r=H({rows:[],header:[]});let S=[];const k=async()=>{u.value=!0,await w.dispatch(x+"fetchExamReport",{uuid:y.student.uuid}).then(d=>{u.value=!1,r.rows=d.rows,r.header=d.header}).catch(d=>{u.value=!1})};return A(async()=>{await k()}),(d,O)=>{const C=t("PageHeaderAction"),P=t("PageHeader"),R=t("DataCell"),B=t("DataRow"),D=t("SimpleTable"),T=t("BaseCard"),j=t("ParentTransition");return a(),i(m,null,[o.student.uuid?(a(),l(P,{key:0,title:n(s)(n(v).meta.label),navs:[{label:n(s)("student.student"),path:"Student"},{label:o.student.contact.name,path:{name:"StudentShow",params:{uuid:o.student.uuid}}}]},{default:e(()=>[c(C,{"additional-actions":n(S)},null,8,["additional-actions"])]),_:1},8,["title","navs"])):f("",!0),c(j,{appear:"",visibility:!0},{default:e(()=>[o.student.uuid?(a(),l(T,{key:0,"is-loading":u.value,"no-padding":"","no-content-padding":""},{title:e(()=>[h(g(n(s)("global.overview",{attribute:n(s)("exam.exam")})),1)]),default:e(()=>[c(D,{header:r.header},{default:e(()=>[(a(!0),i(m,null,b(r.rows,p=>(a(),l(B,{key:p.uuid},{default:e(()=>[(a(!0),i(m,null,b(p,_=>(a(),l(R,{key:_.key},{default:e(()=>[h(g(_.label),1)]),_:2},1024))),128))]),_:2},1024))),128))]),_:1},8,["header"])]),_:1},8,["is-loading"])):f("",!0)]),_:1})],64)}}});export{q as default};
