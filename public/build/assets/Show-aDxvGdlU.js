import{m as P,a as V,e as n,w as e,u as l,F as A,r as s,o as r,q as m,s as u,t as o,y as H,b as c,d as I,i as N,f as T,h as j}from"./app-DCfJDSeM.js";const O={class:"grid grid-cols-1 gap-x-4 gap-y-8 sm:grid-cols-2"},R={name:"StudentDocumentShow"},F=Object.assign(R,{props:{student:{type:Object,default(){return{}}}},setup(i){N();const p=T(),_=j(),b={},g="student/document/",t=P({...b}),S=a=>{Object.assign(t,a)};return(a,f)=>{const $=s("PageHeaderAction"),B=s("PageHeader"),d=s("BaseDataView"),y=s("ListMedia"),D=s("BaseButton"),h=s("ShowButton"),w=s("BaseCard"),k=s("ShowItem"),v=s("ParentTransition");return r(),V(A,null,[n(B,{title:a.$trans(l(p).meta.trans,{attribute:a.$trans(l(p).meta.label)}),navs:[{label:a.$trans("student.student"),path:"Student"},{label:i.student.contact.name,path:{name:"StudentShow",params:{uuid:i.student.uuid}}}]},{default:e(()=>[n($,{name:"StudentDocument",title:a.$trans("student.document.document"),actions:["list"]},null,8,["title"])]),_:1},8,["title","navs"]),n(v,{appear:"",visibility:!0},{default:e(()=>[n(k,{"init-url":g,uuid:l(p).params.uuid,"module-uuid":l(p).params.muuid,onSetItem:S,onRedirectTo:f[1]||(f[1]=C=>l(_).push({name:"StudentDocument",params:{uuid:i.student.uuid}}))},{default:e(()=>[t.uuid?(r(),m(w,{key:0},{title:e(()=>[u(o(t.type.name),1)]),footer:e(()=>[n(h,null,{default:e(()=>[l(H)("student:edit")?(r(),m(D,{key:0,design:"primary",onClick:f[0]||(f[0]=C=>l(_).push({name:"StudentDocumentEdit",params:{uuid:i.student.uuid,muuid:t.uuid}}))},{default:e(()=>[u(o(a.$trans("general.edit")),1)]),_:1})):c("",!0)]),_:1})]),default:e(()=>[I("dl",O,[n(d,{label:a.$trans("student.document.props.title")},{default:e(()=>[u(o(t.title),1)]),_:1},8,["label"]),t.description?(r(),m(d,{key:0,class:"col-span-1 sm:col-span-2",label:a.$trans("student.document.props.description")},{default:e(()=>[u(o(t.description),1)]),_:1},8,["label"])):c("",!0),t.startDate.value?(r(),m(d,{key:1,label:a.$trans("student.document.props.start_date")},{default:e(()=>[u(o(t.startDate.formatted),1)]),_:1},8,["label"])):c("",!0),t.endDate.value?(r(),m(d,{key:2,label:a.$trans("student.document.props.end_date")},{default:e(()=>[u(o(t.endDate.formatted),1)]),_:1},8,["label"])):c("",!0),n(d,{class:"col-span-1 sm:col-span-2"},{default:e(()=>[n(y,{media:t.media,url:`/app/students/${i.student.uuid}/documents/${t.uuid}/`},null,8,["media","url"])]),_:1}),n(d,{label:a.$trans("general.created_at")},{default:e(()=>[u(o(t.createdAt.formatted),1)]),_:1},8,["label"]),n(d,{label:a.$trans("general.updated_at")},{default:e(()=>[u(o(t.updatedAt.formatted),1)]),_:1},8,["label"])])]),_:1})):c("",!0)]),_:1},8,["uuid","module-uuid"])]),_:1})],64)}}});export{F as default};
