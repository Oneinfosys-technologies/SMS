import{f as U,m as C,n as O,r as u,o as b,q as y,w as e,d as _,e as s,b as g,y as k,l as R,u as r,s as m,t as c,a as E,v as z,F as G,h as J,j as K}from"./app-DCfJDSeM.js";const Q={class:"grid grid-cols-3 gap-6"},W={class:"col-span-3 sm:col-span-1"},X={class:"col-span-3 sm:col-span-1"},Y={class:"col-span-3 sm:col-span-1"},Z={class:"col-span-3 sm:col-span-1"},x={class:"col-span-3 sm:col-span-1"},ee={__name:"Filter",props:{preRequisites:{type:Object,default(){return{}}}},emits:["hide"],setup(T,{emit:v}){const i=U(),L=v,$={title:"",authors:[],publishers:[],topics:[],languages:[]},d=C({...$});C({});const n=C({isLoaded:!0,isLoaded:!(i.query.authors||i.query.publishers||i.query.topics)});return O(async()=>{n.authors=i.query.authors?i.query.authors.split(","):[],n.publishers=i.query.publishers?i.query.publishers.split(","):[],n.topics=i.query.topics?i.query.topics.split(","):[],n.languages=i.query.languages?i.query.languages.split(","):[],n.isLoaded=!0}),(p,l)=>{const q=u("BaseInput"),B=u("BaseSelectSearch"),I=u("FilterForm");return b(),y(I,{"init-form":$,form:d,multiple:["authors","publishers","topics","languages"],onHide:l[5]||(l[5]=t=>L("hide"))},{default:e(()=>[_("div",Q,[_("div",W,[s(q,{type:"text",modelValue:d.title,"onUpdate:modelValue":l[0]||(l[0]=t=>d.title=t),name:"title",label:p.$trans("library.book.props.title")},null,8,["modelValue","label"])]),_("div",X,[n.isLoaded?(b(),y(B,{key:0,multiple:"",name:"authors",label:p.$trans("global.select",{attribute:p.$trans("library.book.props.author")}),modelValue:d.authors,"onUpdate:modelValue":l[1]||(l[1]=t=>d.authors=t),"label-prop":"name","value-prop":"uuid","init-search":n.authors,"search-action":"option/list","additional-search-query":{type:"book_author"}},null,8,["label","modelValue","init-search"])):g("",!0)]),_("div",Y,[n.isLoaded?(b(),y(B,{key:0,multiple:"",name:"publishers",label:p.$trans("global.select",{attribute:p.$trans("library.book.props.publisher")}),modelValue:d.publishers,"onUpdate:modelValue":l[2]||(l[2]=t=>d.publishers=t),"label-prop":"name","value-prop":"uuid","init-search":n.publishers,"search-action":"option/list","additional-search-query":{type:"book_publisher"}},null,8,["label","modelValue","init-search"])):g("",!0)]),_("div",Z,[n.isLoaded?(b(),y(B,{key:0,multiple:"",name:"languages",label:p.$trans("global.select",{attribute:p.$trans("library.book.props.language")}),modelValue:d.languages,"onUpdate:modelValue":l[3]||(l[3]=t=>d.languages=t),"label-prop":"name","value-prop":"uuid","init-search":n.languages,"search-action":"option/list","additional-search-query":{type:"book_language"}},null,8,["label","modelValue","init-search"])):g("",!0)]),_("div",x,[n.isLoaded?(b(),y(B,{key:0,multiple:"",name:"topics",label:p.$trans("global.select",{attribute:p.$trans("library.book.props.topic")}),modelValue:d.topics,"onUpdate:modelValue":l[4]||(l[4]=t=>d.topics=t),"label-prop":"name","value-prop":"uuid","init-search":n.topics,"search-action":"option/list","additional-search-query":{type:"book_topic"}},null,8,["label","modelValue","init-search"])):g("",!0)])])]),_:1},8,["form"])}}},te={name:"LibraryBookList"},oe=Object.assign(te,{setup(T){const v=J(),i=K("emitter");let L=["filter"];k("book:create")&&L.unshift("create");let $=[];k("book:export")&&($=["print","pdf","excel"]),k("diocese:create")&&$.unshift("import");const d="library/book/",n=C({authors:[],publishers:[],topics:[],languages:[]}),p=R(!1),l=R(!1),q=C({}),B=t=>{Object.assign(n,t)},I=t=>{Object.assign(q,t)};return(t,o)=>{const w=u("BaseButton"),D=u("PageHeaderAction"),P=u("PageHeader"),S=u("BaseImport"),F=u("ParentTransition"),H=u("TextMuted"),h=u("DataCell"),V=u("FloatingMenuItem"),M=u("FloatingMenu"),j=u("DataRow"),A=u("DataTable"),N=u("ListItem");return b(),y(N,{"init-url":d,"pre-requisites":!0,onSetPreRequisites:B,onSetItems:I},{header:e(()=>[s(P,{title:t.$trans("library.book.book"),navs:[{label:t.$trans("library.library"),path:"Library"}]},{default:e(()=>[s(D,{url:"library/books/",name:"LibraryBook",title:t.$trans("library.book.book"),actions:r(L),"dropdown-actions":r($),onToggleFilter:o[1]||(o[1]=a=>p.value=!p.value),onToggleImport:o[2]||(o[2]=a=>l.value=!l.value)},{after:e(()=>[r(k)("library:config")?(b(),y(w,{key:0,design:"white",onClick:o[0]||(o[0]=a=>r(v).push({name:"LibraryConfig"}))},{default:e(()=>o[10]||(o[10]=[_("i",{class:"fas fa-cog"},null,-1)])),_:1})):g("",!0)]),_:1},8,["title","actions","dropdown-actions"])]),_:1},8,["title","navs"])]),import:e(()=>[s(F,{appear:"",visibility:l.value},{default:e(()=>[s(S,{path:"library/books/import",onCancelled:o[3]||(o[3]=a=>l.value=!1),onHide:o[4]||(o[4]=a=>l.value=!1),onCompleted:o[5]||(o[5]=a=>r(i).emit("listItems"))})]),_:1},8,["visibility"])]),filter:e(()=>[s(F,{appear:"",visibility:p.value},{default:e(()=>[s(ee,{onRefresh:o[6]||(o[6]=a=>r(i).emit("listItems")),"pre-requisites":n,onHide:o[7]||(o[7]=a=>p.value=!1)},null,8,["pre-requisites"])]),_:1},8,["visibility"])]),default:e(()=>[s(F,{appear:"",visibility:!0},{default:e(()=>[s(A,{header:q.headers,meta:q.meta,module:"library.book",onRefresh:o[9]||(o[9]=a=>r(i).emit("listItems"))},{actionButton:e(()=>[r(k)("book:create")?(b(),y(w,{key:0,onClick:o[8]||(o[8]=a=>r(v).push({name:"LibraryBookCreate"}))},{default:e(()=>[m(c(t.$trans("global.add",{attribute:t.$trans("library.book.book")})),1)]),_:1})):g("",!0)]),default:e(()=>[(b(!0),E(G,null,z(q.data,a=>(b(),y(j,{key:a.uuid},{default:e(()=>[s(h,{name:"title"},{default:e(()=>[m(c(a.title)+" ",1),s(H,{block:""},{default:e(()=>[m(c(a.subTitle),1)]),_:2},1024)]),_:2},1024),s(h,{name:"copies"},{default:e(()=>[m(c(a.copiesCount),1)]),_:2},1024),s(h,{name:"author"},{default:e(()=>{var f;return[m(c(((f=a.author)==null?void 0:f.name)||"-"),1)]}),_:2},1024),s(h,{name:"publisher"},{default:e(()=>{var f;return[m(c(((f=a.publisher)==null?void 0:f.name)||"-"),1)]}),_:2},1024),s(h,{name:"topic"},{default:e(()=>{var f;return[m(c(((f=a.topic)==null?void 0:f.name)||"-"),1)]}),_:2},1024),s(h,{name:"isbnNumber"},{default:e(()=>[m(c(a.isbnNumber),1)]),_:2},1024),s(h,{name:"createdAt"},{default:e(()=>[m(c(a.createdAt.formatted),1)]),_:2},1024),s(h,{name:"action"},{default:e(()=>[s(M,null,{default:e(()=>[s(V,{icon:"fas fa-arrow-circle-right",onClick:f=>r(v).push({name:"LibraryBookShow",params:{uuid:a.uuid}})},{default:e(()=>[m(c(t.$trans("general.show")),1)]),_:2},1032,["onClick"]),r(k)("book:edit")?(b(),y(V,{key:0,icon:"fas fa-edit",onClick:f=>r(v).push({name:"LibraryBookEdit",params:{uuid:a.uuid}})},{default:e(()=>[m(c(t.$trans("general.edit")),1)]),_:2},1032,["onClick"])):g("",!0),r(k)("book:create")?(b(),y(V,{key:1,icon:"fas fa-copy",onClick:f=>r(v).push({name:"LibraryBookDuplicate",params:{uuid:a.uuid}})},{default:e(()=>[m(c(t.$trans("general.duplicate")),1)]),_:2},1032,["onClick"])):g("",!0),r(k)("book:delete")?(b(),y(V,{key:2,icon:"fas fa-trash",onClick:f=>r(i).emit("deleteItem",{uuid:a.uuid})},{default:e(()=>[m(c(t.$trans("general.delete")),1)]),_:2},1032,["onClick"])):g("",!0)]),_:2},1024)]),_:2},1024)]),_:2},1024))),128))]),_:1},8,["header","meta"])]),_:1})]),_:1})}}});export{oe as default};
