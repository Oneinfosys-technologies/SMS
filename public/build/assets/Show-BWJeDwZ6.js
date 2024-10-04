import{m as M,a as u,e as n,w as e,u as p,F as c,r as i,o,q as _,d as y,s as r,t as l,v as g,b as $,i as P,f as D,h as N}from"./app-DCfJDSeM.js";const R={class:"space-y-2"},F={class:"flex justify-center gap-2"},O={class:"grid grid-cols-1 gap-x-4 gap-y-8 sm:grid-cols-2"},q=["innerHTML"],U={name:"CommunicationEmailShow"},J=Object.assign(U,{setup(z){P();const f=D(),k=N(),C={},V="communication/email/",t=M({...C}),w=a=>{Object.assign(t,a)},v=a=>t.audiences.filter(m=>m.type===a);return(a,m)=>{const T=i("PageHeaderAction"),B=i("PageHeader"),d=i("ListItemView"),L=i("TextMuted"),A=i("ListContainerVertical"),h=i("BaseCard"),b=i("BaseDataView"),S=i("ListMedia"),j=i("ShowButton"),E=i("DetailLayoutVertical"),H=i("ShowItem"),I=i("ParentTransition");return o(),u(c,null,[n(B,{title:a.$trans(p(f).meta.trans,{attribute:a.$trans(p(f).meta.label)}),navs:[{label:a.$trans("communication.communication"),path:"Communication"},{label:a.$trans("communication.email.email"),path:"CommunicationEmail"}]},{default:e(()=>[n(T,{name:"CommunicationEmail",title:a.$trans("communication.email.email"),actions:["list"]},null,8,["title"])]),_:1},8,["title","navs"]),n(I,{appear:"",visibility:!0},{default:e(()=>[n(H,{"init-url":V,uuid:p(f).params.uuid,"module-uuid":p(f).params.muuid,onSetItem:w,onRedirectTo:m[0]||(m[0]=s=>p(k).push({name:"CommunicationEmail",params:{uuid:t.uuid}}))},{default:e(()=>[t.uuid?(o(),_(E,{key:0},{detail:e(()=>[y("div",R,[n(h,{"no-padding":"","no-content-padding":""},{title:e(()=>[r(" #"+l(t.subjectExcerpt),1)]),action:e(()=>m[1]||(m[1]=[])),default:e(()=>[n(A,null,{default:e(()=>[n(d,{label:a.$trans("communication.email.props.subject")},{default:e(()=>[r(l(t.subject),1)]),_:1},8,["label"]),n(d,{label:a.$trans("communication.email.props.audience")},{default:e(()=>[r(l(t.studentAudienceType.label)+" ",1),(o(!0),u(c,null,g(v("student"),s=>(o(),_(L,{block:""},{default:e(()=>[r(l(s.name),1)]),_:2},1024))),256))]),_:1},8,["label"]),n(d,{label:a.$trans("communication.email.props.audience")},{default:e(()=>[t.employeeAudienceType.value?(o(),u(c,{key:0},[r(l(t.employeeAudienceType.label)+" ",1),(o(!0),u(c,null,g(v("employee"),s=>(o(),_(L,{block:""},{default:e(()=>[r(l(s.name),1)]),_:2},1024))),256))],64)):(o(),u(c,{key:1},[r("-")],64))]),_:1},8,["label"]),n(d,{label:a.$trans("general.created_at")},{default:e(()=>[r(l(t.createdAt.formatted),1)]),_:1},8,["label"]),n(d,{label:a.$trans("general.updated_at")},{default:e(()=>[r(l(t.updatedAt.formatted),1)]),_:1},8,["label"])]),_:1})]),_:1})])]),default:e(()=>[t.uuid?(o(),_(h,{key:0},{title:e(()=>[y("div",F,l(t.subject),1)]),footer:e(()=>[n(j)]),default:e(()=>[y("dl",O,[n(b,{class:"col-span-1 sm:col-span-2"},{default:e(()=>[y("div",{innerHTML:t.content},null,8,q)]),_:1}),n(b,{label:a.$trans("communication.email.props.inclusion")},{default:e(()=>[(o(!0),u(c,null,g(t.inclusionList,s=>(o(),u("div",{key:s},l(s),1))),128))]),_:1},8,["label"]),n(b,{label:a.$trans("communication.email.props.exclusion")},{default:e(()=>[(o(!0),u(c,null,g(t.exclusionList,s=>(o(),u("div",{key:s},l(s),1))),128))]),_:1},8,["label"]),t.media.length>0?(o(),_(b,{key:0,class:"col-span-1 sm:col-span-2"},{default:e(()=>[n(S,{media:t.media,url:`/app/communication/emails/${t.uuid}/`},null,8,["media","url"])]),_:1})):$("",!0)])]),_:1})):$("",!0)]),_:1})):$("",!0)]),_:1},8,["uuid","module-uuid"])]),_:1})],64)}}});export{J as default};
