import{m as H,a as p,e as o,w as a,u as i,F as m,r as s,o as r,q as b,s as n,t as l,y as I,b as w,d as M,v as B,i as N,f as E,h as L}from"./app-DCfJDSeM.js";const j={class:"grid grid-cols-1 gap-x-4 gap-y-8 sm:grid-cols-2"},F={name:"ResourceDownloadShow"},U=Object.assign(F,{setup(O){N();const _=E(),y=L(),k={},h="resource/download/",e=H({...k}),A=t=>{Object.assign(e,t)},g=t=>e.audiences.filter(c=>c.type===t);return(t,c)=>{const v=s("PageHeaderAction"),D=s("PageHeader"),f=s("TextMuted"),d=s("BaseDataView"),R=s("ListMedia"),T=s("BaseButton"),S=s("ShowButton"),P=s("BaseCard"),C=s("ShowItem"),V=s("ParentTransition");return r(),p(m,null,[o(D,{title:t.$trans(i(_).meta.trans,{attribute:t.$trans(i(_).meta.label)}),navs:[{label:t.$trans("resource.resource"),path:"Resource"},{label:t.$trans("resource.download.download"),path:"ResourceDownload"}]},{default:a(()=>[o(v,{name:"ResourceDownload",title:t.$trans("resource.download.download"),actions:["list"]},null,8,["title"])]),_:1},8,["title","navs"]),o(V,{appear:"",visibility:!0},{default:a(()=>[o(C,{"init-url":h,uuid:i(_).params.uuid,"module-uuid":i(_).params.muuid,onSetItem:A,onRedirectTo:c[1]||(c[1]=u=>i(y).push({name:"ResourceDownload",params:{uuid:e.uuid}}))},{default:a(()=>[e.uuid?(r(),b(P,{key:0},{title:a(()=>[n(l(e.title),1)]),footer:a(()=>[o(S,null,{default:a(()=>[i(I)("download:edit")&&e.isEditable?(r(),b(T,{key:0,design:"primary",onClick:c[0]||(c[0]=u=>i(y).push({name:"ResourceDownloadEdit",params:{uuid:e.uuid}}))},{default:a(()=>[n(l(t.$trans("general.edit")),1)]),_:1})):w("",!0)]),_:1})]),default:a(()=>[M("dl",j,[o(d,{label:t.$trans("employee.employee")},{default:a(()=>{var u;return[n(l(((u=e.employee)==null?void 0:u.name)||"-")+" ",1),o(f,{block:""},{default:a(()=>{var $;return[n(l((($=e.employee)==null?void 0:$.designation)||""),1)]}),_:1})]}),_:1},8,["label"]),o(d,{label:t.$trans("resource.download.props.published_at")},{default:a(()=>[n(l(e.publishedAt.formatted),1)]),_:1},8,["label"]),o(d,{label:t.$trans("resource.download.props.expires_at")},{default:a(()=>[n(l(e.expiresAt.formatted),1)]),_:1},8,["label"]),e.isPublic?w("",!0):(r(),p(m,{key:0},[o(d,{label:t.$trans("resource.download.props.audience")},{default:a(()=>[n(l(e.studentAudienceType.label)+" ",1),(r(!0),p(m,null,B(g("student"),u=>(r(),b(f,{block:""},{default:a(()=>[n(l(u.name),1)]),_:2},1024))),256))]),_:1},8,["label"]),o(d,{label:t.$trans("resource.download.props.audience")},{default:a(()=>[e.employeeAudienceType.value?(r(),p(m,{key:0},[n(l(e.employeeAudienceType.label)+" ",1),(r(!0),p(m,null,B(g("employee"),u=>(r(),b(f,{block:""},{default:a(()=>[n(l(u.name),1)]),_:2},1024))),256))],64)):(r(),p(m,{key:1},[n("-")],64))]),_:1},8,["label"])],64)),o(d,{label:t.$trans("resource.download.props.description"),class:"col-span-1 sm:col-span-2"},{default:a(()=>[n(l(e.description),1)]),_:1},8,["label"]),o(d,{class:"col-span-1 sm:col-span-2"},{default:a(()=>[o(R,{media:e.media,url:`/app/resource/downloads/${e.uuid}/`},null,8,["media","url"])]),_:1}),o(d,{label:t.$trans("general.created_at")},{default:a(()=>[n(l(e.createdAt.formatted),1)]),_:1},8,["label"]),o(d,{label:t.$trans("general.updated_at")},{default:a(()=>[n(l(e.updatedAt.formatted),1)]),_:1},8,["label"])])]),_:1})):w("",!0)]),_:1},8,["uuid","module-uuid"])]),_:1})],64)}}});export{U as default};
