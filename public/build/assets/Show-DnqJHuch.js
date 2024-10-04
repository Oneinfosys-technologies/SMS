import{m as A,a as y,e as s,w as e,u as i,F as g,r as n,o as r,q as m,s as u,t as l,y as B,b as f,d as H,v,i as I,f as M,h as N}from"./app-DCfJDSeM.js";const E={class:"grid grid-cols-1 gap-x-4 gap-y-8 sm:grid-cols-2"},F={name:"ResourceDiaryShow"},U=Object.assign(F,{setup(O){I();const p=M(),b=N(),$={},h="resource/diary/",a=A({...$}),k=o=>{Object.assign(a,o)};return(o,_)=>{const D=n("PageHeaderAction"),R=n("PageHeader"),w=n("TextMuted"),d=n("BaseDataView"),S=n("ListMedia"),V=n("ViewLog"),C=n("BaseButton"),L=n("ShowButton"),P=n("BaseCard"),T=n("ShowItem"),j=n("ParentTransition");return r(),y(g,null,[s(R,{title:o.$trans(i(p).meta.trans,{attribute:o.$trans(i(p).meta.label)}),navs:[{label:o.$trans("resource.resource"),path:"Resource"},{label:o.$trans("resource.diary.diary"),path:"ResourceDiary"}]},{default:e(()=>[s(D,{name:"ResourceDiary",title:o.$trans("resource.diary.diary"),actions:["list"]},null,8,["title"])]),_:1},8,["title","navs"]),s(j,{appear:"",visibility:!0},{default:e(()=>[s(T,{"init-url":h,uuid:i(p).params.uuid,"module-uuid":i(p).params.muuid,onSetItem:k,onRedirectTo:_[1]||(_[1]=t=>i(b).push({name:"ResourceDiary",params:{uuid:a.uuid}}))},{default:e(()=>[a.uuid?(r(),m(P,{key:0},{title:e(()=>[u(l(a.date.formatted),1)]),footer:e(()=>[s(L,null,{default:e(()=>[i(B)("student-diary:edit")&&a.isEditable?(r(),m(C,{key:0,design:"primary",onClick:_[0]||(_[0]=t=>i(b).push({name:"ResourceDiaryEdit",params:{uuid:a.uuid}}))},{default:e(()=>[u(l(o.$trans("general.edit")),1)]),_:1})):f("",!0)]),_:1})]),default:e(()=>[H("dl",E,[s(d,{label:o.$trans("academic.course.course")},{default:e(()=>[(r(!0),y(g,null,v(a.records,t=>{var c;return r(),y("div",null,[u(l(((c=t.batch.course)==null?void 0:c.name)+" "+t.batch.name)+" ",1),t.subject?(r(),m(w,{key:0},{default:e(()=>[u(l(t.subject.name),1)]),_:2},1024)):f("",!0)])}),256))]),_:1},8,["label"]),s(d,{label:o.$trans("employee.employee")},{default:e(()=>{var t;return[u(l(((t=a.employee)==null?void 0:t.name)||"-")+" ",1),s(w,{block:""},{default:e(()=>{var c;return[u(l(((c=a.employee)==null?void 0:c.designation)||""),1)]}),_:1})]}),_:1},8,["label"]),(r(!0),y(g,null,v(a.details,t=>(r(),m(d,{class:"mt-4 col-span-1 sm:col-span-2",key:t.uuid,label:t.heading},{default:e(()=>[u(l(t.description),1)]),_:2},1032,["label"]))),128)),s(d,{class:"col-span-1 sm:col-span-2"},{default:e(()=>[s(S,{media:a.media,url:`/app/resource/diaries/${a.uuid}/`},null,8,["media","url"])]),_:1}),i(B)("student-diary:view-log")?(r(),m(d,{key:0,class:"col-span-1 sm:col-span-2"},{default:e(()=>[s(V,{"view-logs":a.viewLogs},null,8,["view-logs"])]),_:1})):f("",!0),s(d,{label:o.$trans("general.created_at")},{default:e(()=>[u(l(a.createdAt.formatted),1)]),_:1},8,["label"]),s(d,{label:o.$trans("general.updated_at")},{default:e(()=>[u(l(a.updatedAt.formatted),1)]),_:1},8,["label"])])]),_:1})):f("",!0)]),_:1},8,["uuid","module-uuid"])]),_:1})],64)}}});export{U as default};
