import{m as k,a as C,e as n,w as t,u as e,F as P,r as i,o as p,q as _,s as l,t as s,y as V,b as y,d as x,i as j,f as H,h as I,j as N}from"./app-DCfJDSeM.js";const $={class:"grid grid-cols-1 gap-x-4 gap-y-8 sm:grid-cols-2"},D={name:"EmployeeAttendanceTimesheetShow"},O=Object.assign(D,{setup(R){j();const r=H(),c=I(),a=N("$trans"),h={},f="employee/attendance/timesheet/",o=k({...h}),b=u=>{Object.assign(o,u)};return(u,d)=>{const g=i("PageHeaderAction"),B=i("PageHeader"),m=i("BaseDataView"),A=i("BaseButton"),T=i("ShowButton"),w=i("BaseCard"),E=i("ShowItem"),S=i("ParentTransition");return p(),C(P,null,[n(B,{title:e(a)(e(r).meta.trans,{attribute:e(a)(e(r).meta.label)}),navs:[{label:e(a)("employee.employee"),path:"Employee"},{label:e(a)("employee.attendance.attendance"),path:"EmployeeAttendance"},{label:e(a)("employee.attendance.timesheet.timesheet"),path:"EmployeeAttendanceTimesheetList"}]},{default:t(()=>[n(g,{name:"EmployeeAttendanceTimesheet",title:e(a)("employee.attendance.timesheet.timesheet"),actions:["list"]},null,8,["title"])]),_:1},8,["title","navs"]),n(S,{appear:"",visibility:!0},{default:t(()=>[n(E,{"init-url":f,uuid:e(r).params.uuid,onSetItem:b,onRedirectTo:d[1]||(d[1]=v=>e(c).push({name:"EmployeeAttendanceTimesheet"}))},{default:t(()=>[o.uuid?(p(),_(w,{key:0},{title:t(()=>[l(s(o.employee.name),1)]),footer:t(()=>[n(T,null,{default:t(()=>[e(V)("timesheet:edit")?(p(),_(A,{key:0,design:"primary",onClick:d[0]||(d[0]=v=>e(c).push({name:"EmployeeAttendanceTimesheetEdit",params:{uuid:o.uuid}}))},{default:t(()=>[l(s(e(a)("general.edit")),1)]),_:1})):y("",!0)]),_:1})]),default:t(()=>[x("dl",$,[n(m,{label:e(a)("employee.department.department")},{default:t(()=>[l(s(o.employee.department),1)]),_:1},8,["label"]),n(m,{label:e(a)("employee.designation.designation")},{default:t(()=>[l(s(o.employee.designation),1)]),_:1},8,["label"]),n(m,{label:e(a)("employee.attendance.timesheet.props.duration")},{default:t(()=>[l(s(o.duration),1)]),_:1},8,["label"]),n(m,{label:e(a)("employee.attendance.timesheet.props.in_at")},{default:t(()=>[l(s(o.inAt.formatted),1)]),_:1},8,["label"]),n(m,{label:e(a)("employee.attendance.timesheet.props.out_at")},{default:t(()=>[l(s(o.outAt.formatted),1)]),_:1},8,["label"]),n(m,{label:e(a)("general.created_at")},{default:t(()=>[l(s(o.createdAt.formatted),1)]),_:1},8,["label"]),n(m,{label:e(a)("general.updated_at")},{default:t(()=>[l(s(o.updatedAt.formatted),1)]),_:1},8,["label"])])]),_:1})):y("",!0)]),_:1},8,["uuid"])]),_:1})],64)}}});export{O as default};
