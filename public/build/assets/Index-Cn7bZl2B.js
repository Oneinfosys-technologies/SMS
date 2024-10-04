import{f as L,m as N,n as R,r as s,o as d,q as f,w as e,d as C,e as t,y as p,l as j,u as r,s as i,t as l,b as k,a as O,v as S,F as U,h as E,j as q}from"./app-DCfJDSeM.js";import{_ as x}from"./ModuleDropdown-CAwsQhjt.js";const z={class:"grid grid-cols-3 gap-6"},G={class:"col-span-3 sm:col-span-1"},J={class:"col-span-3 sm:col-span-1"},K={__name:"Filter",emits:["hide"],setup(D,{emit:_}){L();const b=_,g={registrationNumber:"",modelNumber:""},m=N({...g}),T=N({isLoaded:!0});return R(async()=>{T.isLoaded=!0}),(c,u)=>{const h=s("BaseInput"),o=s("FilterForm");return d(),f(o,{"init-form":g,form:m,multiple:[],onHide:u[2]||(u[2]=n=>b("hide"))},{default:e(()=>[C("div",z,[C("div",G,[t(h,{type:"text",modelValue:m.registrationNumber,"onUpdate:modelValue":u[0]||(u[0]=n=>m.registrationNumber=n),name:"registrationNumber",label:c.$trans("transport.vehicle.props.registration_number")},null,8,["modelValue","label"])]),C("div",J,[t(h,{type:"text",modelValue:m.modelNumber,"onUpdate:modelValue":u[1]||(u[1]=n=>m.modelNumber=n),name:"modelNumber",label:c.$trans("transport.vehicle.props.model_number")},null,8,["modelValue","label"])])])]),_:1},8,["form"])}}},Q={name:"TransportVehicleList"},Y=Object.assign(Q,{setup(D){const _=E(),b=q("emitter");let g=["filter"];p("vehicle:config")&&g.push("config"),p("vehicle:create")&&g.unshift("create");let m=[];p("vehicle:export")&&(m=["print","pdf","excel"]);const T="transport/vehicle/",c=j(!1),u=N({}),h=o=>{Object.assign(u,o)};return(o,n)=>{const w=s("PageHeaderAction"),B=s("PageHeader"),y=s("ParentTransition"),v=s("DataCell"),F=s("TextMuted"),$=s("FloatingMenuItem"),I=s("FloatingMenu"),M=s("DataRow"),P=s("BaseButton"),A=s("DataTable"),H=s("ListItem");return d(),f(H,{"init-url":T,onSetItems:h},{header:e(()=>[t(B,{title:o.$trans("transport.vehicle.vehicle"),navs:[{label:o.$trans("transport.transport"),path:"Transport"}]},{default:e(()=>[t(w,{url:"transport/vehicles/",name:"TransportVehicle",title:o.$trans("transport.vehicle.vehicle"),actions:r(g),"dropdown-actions":r(m),"config-path":"TransportVehicleConfigDocumentType",onToggleFilter:n[0]||(n[0]=a=>c.value=!c.value)},{moduleOption:e(()=>[t(x)]),_:1},8,["title","actions","dropdown-actions"])]),_:1},8,["title","navs"])]),filter:e(()=>[t(y,{appear:"",visibility:c.value},{default:e(()=>[t(K,{onRefresh:n[1]||(n[1]=a=>r(b).emit("listItems")),onHide:n[2]||(n[2]=a=>c.value=!1)})]),_:1},8,["visibility"])]),default:e(()=>[t(y,{appear:"",visibility:!0},{default:e(()=>[t(A,{header:u.headers,meta:u.meta,module:"transport.vehicle",onRefresh:n[4]||(n[4]=a=>r(b).emit("listItems"))},{actionButton:e(()=>[r(p)("vehicle:create")?(d(),f(P,{key:0,onClick:n[3]||(n[3]=a=>r(_).push({name:"TransportVehicleCreate"}))},{default:e(()=>[i(l(o.$trans("global.add",{attribute:o.$trans("transport.vehicle.vehicle")})),1)]),_:1})):k("",!0)]),default:e(()=>[(d(!0),O(U,null,S(u.data,a=>(d(),f(M,{key:a.uuid},{default:e(()=>[t(v,{name:"name"},{default:e(()=>[i(l(a.name),1)]),_:2},1024),t(v,{name:"registrationNumber"},{default:e(()=>[i(l(a.registrationNumber)+" ",1),t(F,{block:""},{default:e(()=>[i(l(a.registrationPlace),1)]),_:2},1024)]),_:2},1024),t(v,{name:"registrationDate"},{default:e(()=>[i(l(a.registrationDate.formatted),1)]),_:2},1024),t(v,{name:"modelNumber"},{default:e(()=>[i(l(a.modelNumber)+" ",1),t(F,{block:""},{default:e(()=>[i(l(a.make),1)]),_:2},1024)]),_:2},1024),t(v,{name:"createdAt"},{default:e(()=>[i(l(a.createdAt.formatted),1)]),_:2},1024),t(v,{name:"action"},{default:e(()=>[t(I,null,{default:e(()=>[t($,{icon:"fas fa-arrow-circle-right",onClick:V=>r(_).push({name:"TransportVehicleShow",params:{uuid:a.uuid}})},{default:e(()=>[i(l(o.$trans("general.show")),1)]),_:2},1032,["onClick"]),r(p)("vehicle:edit")?(d(),f($,{key:0,icon:"fas fa-edit",onClick:V=>r(_).push({name:"TransportVehicleEdit",params:{uuid:a.uuid}})},{default:e(()=>[i(l(o.$trans("general.edit")),1)]),_:2},1032,["onClick"])):k("",!0),r(p)("vehicle:create")?(d(),f($,{key:1,icon:"fas fa-copy",onClick:V=>r(_).push({name:"TransportVehicleDuplicate",params:{uuid:a.uuid}})},{default:e(()=>[i(l(o.$trans("general.duplicate")),1)]),_:2},1032,["onClick"])):k("",!0),r(p)("vehicle:delete")?(d(),f($,{key:2,icon:"fas fa-trash",onClick:V=>r(b).emit("deleteItem",{uuid:a.uuid})},{default:e(()=>[i(l(o.$trans("general.delete")),1)]),_:2},1032,["onClick"])):k("",!0)]),_:2},1024)]),_:2},1024)]),_:2},1024))),128))]),_:1},8,["header","meta"])]),_:1})]),_:1})}}});export{Y as default};
