import{C as M,D as Z,b as w,E as W,G as R,N,H as ee,I as _,F as S,J as te,K as se,L as re,M as oe,O as x,P as ne,Q as ie,R as ae,U as le,V as de,n as ce,X as fe,Y as ue,Z as me,_ as pe,$ as he,a0 as ge,a1 as ye,a2 as be,a3 as ve,a4 as xe,d as ke,r as we,x as _e,i as Ce,W as Q,a as g,o as y,u as b,g as Se,w as $e,e as c,k as L,f as H,t as C,v as Re,y as Ee,h as j,m as Me,j as F,a5 as U}from"./app-Dl3ghlgD.js";import{_ as z}from"./AppLogoIcon.vue_vue_type_script_setup_true_lang-CKaRu_f7.js";import{u as Te}from"./useSweetAlert-Cof9YfZk.js";import{_ as Ve}from"./AppLayout.vue_vue_type_script_setup_true_lang-KsND7BiD.js";import{Q as B}from"./browser-La4KB7an.js";import{c as T}from"./createLucideIcon-fBfe8z8E.js";import"./index-BFUXN-lK.js";import"./useForwardExpose-C6pCFvAC.js";import"./RovingFocusGroup-D8pp2cKb.js";import"./x-CTgxqL8i.js";import"./AppLogo.vue_vue_type_script_setup_true_lang-D37ZCpxo.js";import"./user-CHZj7BuD.js";import"./qr-code-B_nGl-lq.js";import"./scan-line-DOQmCL-x.js";import"./calendar-days-DUZpzLiL.js";import"./ticket-BCZm-KTS.js";/**
 * @license lucide-vue-next v0.468.0 - ISC
 *
 * This source code is licensed under the ISC license.
 * See the LICENSE file in the root directory of this source tree.
 */const Be=T("ListIcon",[["path",{d:"M3 12h.01",key:"nlz23k"}],["path",{d:"M3 18h.01",key:"1tta3j"}],["path",{d:"M3 6h.01",key:"1rqtza"}],["path",{d:"M8 12h13",key:"1za7za"}],["path",{d:"M8 18h13",key:"1lx6n3"}],["path",{d:"M8 6h13",key:"ik3vkj"}]]);/**
 * @license lucide-vue-next v0.468.0 - ISC
 *
 * This source code is licensed under the ISC license.
 * See the LICENSE file in the root directory of this source tree.
 */const Ne=T("PlusIcon",[["path",{d:"M5 12h14",key:"1ays0h"}],["path",{d:"M12 5v14",key:"s699le"}]]);/**
 * @license lucide-vue-next v0.468.0 - ISC
 *
 * This source code is licensed under the ISC license.
 * See the LICENSE file in the root directory of this source tree.
 */const Ae=T("PrinterIcon",[["path",{d:"M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2",key:"143wyd"}],["path",{d:"M6 9V3a1 1 0 0 1 1-1h10a1 1 0 0 1 1 1v6",key:"1itne7"}],["rect",{x:"6",y:"14",width:"12",height:"8",rx:"1",key:"1ue0tg"}]]);/**
 * @license lucide-vue-next v0.468.0 - ISC
 *
 * This source code is licensed under the ISC license.
 * See the LICENSE file in the root directory of this source tree.
 */const Pe=T("Trash2Icon",[["path",{d:"M3 6h18",key:"d0wm0j"}],["path",{d:"M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6",key:"4alrt4"}],["path",{d:"M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2",key:"v07s0e"}],["line",{x1:"10",x2:"10",y1:"11",y2:"17",key:"1uufr5"}],["line",{x1:"14",x2:"14",y1:"11",y2:"17",key:"xtxkd"}]]);/**
* @vue/server-renderer v3.5.16
* (c) 2018-present Yuxi (Evan) You and Vue contributors
* @license MIT
**/const Qe=le(",key,ref,innerHTML,textContent,ref_key,ref_for");function Le(e,t){let s="";for(const r in e){if(Qe(r)||de(r)||t==="textarea"&&r==="value")continue;const o=e[r];r==="class"?s+=` class="${je(o)}"`:r==="style"?s+=` style="${Fe(o)}"`:r==="className"?s+=` class="${String(o)}"`:s+=He(r,o,t)}return s}function He(e,t,s){if(!me(t))return"";const r=s&&(s.indexOf("-")>0||pe(s))?e:he[e]||e.toLowerCase();return ge(r)?ye(t)?` ${r}`:"":be(r)?t===""?` ${r}`:` ${r}="${x(t)}"`:(console.warn(`[@vue/server-renderer] Skipped rendering unsafe attribute name: ${r}`),"")}function je(e){return x(ce(e))}function Fe(e){if(!e)return"";if(_(e))return x(e);const t=fe(e);return x(ue(t))}const{ensureValidVNode:At}=M;function Ue(e,t,s,r,o){e("<!--teleport start-->");const n=o.appContext.provides[W],i=n.__teleportBuffers||(n.__teleportBuffers={}),f=i[s]||(i[s]=[]),p=f.length;let h;if(r)t(e),h="<!--teleport start anchor--><!--teleport anchor-->";else{const{getBuffer:m,push:u}=q();u("<!--teleport start anchor-->"),t(u),u("<!--teleport anchor-->"),h=m()}f.splice(p,0,h),e("<!--teleport end-->")}{const e=ve(),t=(s,r)=>{let o;return(o=e[s])||(o=e[s]=[]),o.push(r),n=>{o.length>1?o.forEach(i=>i(n)):o[0](n)}};t("__VUE_INSTANCE_SETTERS__",s=>s),t("__VUE_SSR_SETTERS__",s=>s)}function ze(e,t){throw new Error("On-the-fly template compilation is not supported in the ESM build of @vue/server-renderer. All templates must be pre-compiled into render functions.")}const{createComponentInstance:De,setCurrentRenderingInstance:D,setupComponent:Ie,renderComponentRoot:I,normalizeVNode:Oe,pushWarningContext:Pt,popWarningContext:Qt}=M;function q(){let e=!1;const t=[];return{getBuffer(){return t},push(s){const r=_(s);if(e&&r){t[t.length-1]+=s;return}t.push(s),e=r,(R(s)||ne(s)&&s.hasAsync)&&(t.hasAsync=!0)}}}function G(e,t=null,s){const r=e.component=De(e,t,null),o=Ie(r,!0),n=R(o);let i=r.sp;return n||i?Promise.resolve(o).then(()=>{if(n&&(i=r.sp),i)return Promise.all(i.map(p=>p.call(r.proxy)))}).catch(N).then(()=>O(r,s)):O(r,s)}function O(e,t){const s=e.type,{getBuffer:r,push:o}=q();if(ee(s)){let n=I(e);if(!s.props)for(const i in e.attrs)i.startsWith("data-v-")&&((n.props||(n.props={}))[i]="");E(o,e.subTree=n,e,t)}else{(!e.render||e.render===N)&&!e.ssrRender&&!s.ssrRender&&_(s.template)&&(s.ssrRender=ze(s.template));const n=e.ssrRender||s.ssrRender;if(n){let i=e.inheritAttrs!==!1?e.attrs:void 0,f=!1,p=e;for(;;){const m=p.vnode.scopeId;m&&(f||(i={...i},f=!0),i[m]="");const u=p.parent;if(u&&u.subTree&&u.subTree===p.vnode)p=u;else break}if(t){f||(i={...i});const m=t.trim().split(" ");for(let u=0;u<m.length;u++)i[m[u]]=""}const h=D(e);try{n(e.proxy,o,e,i,e.props,e.setupState,e.data,e.ctx)}finally{D(h)}}else e.render&&e.render!==N?E(o,e.subTree=I(e),e,t):(s.name||s.__file,o("<!---->"))}return r()}function E(e,t,s,r){const{type:o,shapeFlag:n,children:i,dirs:f,props:p}=t;switch(f&&(t.props=qe(t,p,f)),o){case oe:e(x(i));break;case se:e(i?`<!--${re(i)}-->`:"<!---->");break;case te:e(i);break;case S:t.slotScopeIds&&(r=(r?r+" ":"")+t.slotScopeIds.join(" ")),e("<!--[-->"),P(e,i,s,r),e("<!--]-->");break;default:n&1?We(e,t,s,r):n&6?e(G(t,s,r)):n&64?Ge(e,t,s,r):n&128&&E(e,t.ssContent,s,r)}}function P(e,t,s,r){for(let o=0;o<t.length;o++)E(e,Oe(t[o]),s,r)}function We(e,t,s,r){const o=t.type;let{props:n,children:i,shapeFlag:f,scopeId:p}=t,h=`<${o}`;n&&(h+=Le(n,o)),p&&(h+=` ${p}`);let m=s,u=t;for(;m&&u===m.subTree;)u=m.vnode,u.scopeId&&(h+=` ${u.scopeId}`),m=m.parent;if(r&&(h+=` ${r}`),e(h+">"),!ae(o)){let a=!1;n&&(n.innerHTML?(a=!0,e(n.innerHTML)):n.textContent?(a=!0,e(x(n.textContent))):o==="textarea"&&n.value&&(a=!0,e(x(n.value)))),a||(f&8?e(x(i)):f&16&&P(e,i,s,r)),e(`</${o}>`)}}function qe(e,t,s){const r=[];for(let o=0;o<s.length;o++){const n=s[o],{dir:{getSSRProps:i}}=n;if(i){const f=i(n,e);f&&r.push(f)}}return ie(t||{},...r)}function Ge(e,t,s,r){const o=t.props&&t.props.to,n=t.props&&t.props.disabled;if(!o)return[];if(!_(o))return[];Ue(e,i=>{P(i,t.children,s,r)},o,n||n==="",s)}const{isVNode:Ke}=M;function $(e,t,s){if(!e.hasAsync)return t+J(e);let r=t;for(let o=s;o<e.length;o+=1){const n=e[o];if(_(n)){r+=n;continue}if(R(n))return n.then(f=>(e[o]=f,$(e,r,o)));const i=$(n,r,0);if(R(i))return i.then(f=>(e[o]=f,$(e,"",o)));r=i}return r}function K(e){return $(e,"",0)}function J(e){let t="";for(let s=0;s<e.length;s++){let r=e[s];_(r)?t+=r:t+=J(r)}return t}async function A(e,t={}){if(Ke(e))return A(Z({render:()=>e}),t);const s=w(e._component,e._props);s.appContext=e._context,e.provide(W,t);const r=await G(s),o=await K(r);if(await Je(t),t.__watcherHandles)for(const n of t.__watcherHandles)n();return o}async function Je(e){if(e.__teleportBuffers){e.teleports=e.teleports||{};for(const t in e.__teleportBuffers)e.teleports[t]=await K(await Promise.all([e.__teleportBuffers[t]]))}}const{isVNode:Lt}=M;xe();const Xe={class:"py-12"},Ye={class:"mx-auto max-w-7xl sm:px-6 lg:px-8"},Ze={class:"mb-8 overflow-hidden bg-white p-6 shadow-sm sm:rounded-lg dark:bg-gray-800"},et=["value"],tt={key:0,class:"space-y-8"},st={class:"overflow-hidden bg-white p-6 shadow-sm sm:rounded-lg dark:bg-gray-800"},rt={class:"text-lg font-medium text-gray-900 dark:text-gray-100"},ot={class:"flex-grow"},nt={key:0,class:"mt-1 text-sm text-red-500"},it=["disabled"],at={class:"overflow-hidden bg-white p-6 shadow-sm sm:rounded-lg dark:bg-gray-800"},lt={class:"flex flex-col items-start justify-between gap-4 sm:flex-row sm:items-center"},dt={key:0,class:"mt-6 text-center text-gray-500"},ct={key:1,class:"mt-6 grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3"},ft={class:"text-lg font-semibold dark:text-white"},ut=["id","onVnodeMounted"],mt={class:"mt-2 text-center font-mono text-xs break-all text-gray-400"},pt={class:"mt-4 flex w-full justify-between"},ht=["onClick"],gt=["onClick"],yt={key:1,class:"mt-8 flex flex-col items-center justify-center rounded-lg border-2 border-dashed border-gray-300 p-12 text-center dark:border-gray-600"},Ht=ke({__name:"StaticQR",props:{events:{},selectedEvent:{},filters:{}},setup(e){const t=e,{confirmDelete:s}=Te(),r=we(t.filters.event_id||""),o=_e({label:""}),n=()=>{t.selectedEvent&&o.post(route("admin.events.static-qrs.store",t.selectedEvent.id),{onSuccess:()=>o.reset(),preserveScroll:!0})},i=a=>{const l=document.getElementById(`qr-canvas-${a.id}`),d=route("public.checkin.form",{code:a.code});l&&B.toCanvas(l,d,{width:150,margin:1,color:{dark:"#111827",light:"#FFFFFF"}})},f=a=>{s(()=>{Q.delete(route("admin.events.static-qrs.destroy",{static_qr:a.id}),{preserveScroll:!0})})},p=async(a,l)=>{const d=await B.toDataURL(route("public.checkin.form",{code:a.code}),{width:380,margin:2,errorCorrectionLevel:"H"}),k=await A(U(z,{class:"h-8 w-8"}));m(`
        <div class="ticket">
            <div class="header">
                <div class="logo">${k}</div>
                <h1>${l.name}</h1>
            </div>
            <div class="main-content">
                <img src="${d}" alt="QR Code" />
                <h2>${a.label}</h2>
            </div>
        </div>
    `,`Cetak QR Code - ${a.label}`)},h=async(a,l)=>{if(!a||a.length===0)return;const d=await A(U(z,{class:"h-8 w-8"})),k=a.map(V=>B.toDataURL(route("public.checkin.form",{code:V.code}),{width:380,margin:2,errorCorrectionLevel:"H"})),v=await Promise.all(k),X=a.map((V,Y)=>`
        <div class="ticket">
            <div class="header">
                <div class="logo">${d}</div>
                <h1>${l.name}</h1>
            </div>
            <div class="main-content">
                <img src="${v[Y]}" alt="QR Code" />
                <h2>${V.label}</h2>
            </div>
        </div>
    `).join("");m(X,`Cetak Semua QR - ${l.name}`,!0)},m=(a,l,d=!1)=>{const k=`
        <html>
            <head>
                <title>${l}</title>
                <style>
                    @page {
                        size: A6;
                        margin: 0;
                    }
                    * {
                        box-sizing: border-box;
                    }
                    body {
                        margin: 0;
                        font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
                        ${d?"":"display: flex; align-items: center; justify-content: center; width: 10.5cm; height: 14.8cm;"}
                    }
                    .ticket {
                        padding: 0.5cm;
                        width: 10.5cm;
                        height: 14.8cm;
                        display: flex;
                        flex-direction: column;
                        justify-content: space-between;
                        border: 1px solid #ddd;
                        page-break-after: always;
                    }
                    .ticket:last-child {
                        page-break-after: auto;
                    }
                    .header {
                        display: flex; align-items: center; gap: 12px;
                        border-bottom: 1px solid #ddd; padding-bottom: 12px; flex-shrink: 0;
                    }
                    .header .logo { width: 40px; height: 40px; flex-shrink: 0; }
                    .header h1 { font-size: 14px; margin: 0; color: #333; text-align: left; line-height: 1.3; }
                    .main-content {
                        flex-grow: 1; display: flex; flex-direction: column;
                        align-items: center; justify-content: center; padding: 12px 0;
                    }
                    .main-content img { max-width: 90%; height: auto; }
                    .main-content h2 { font-size: 22px; font-weight: bold; margin: 12px 0 0 0; color: #000; }
                </style>
            </head>
            <body>
                ${a}
            </body>
        </html>
    `,v=window.open("","_blank");v&&(v.document.write(k),v.document.close(),v.focus(),setTimeout(()=>{v.print(),v.close()},250))};Ce(r,a=>{Q.get(route("admin.events.static-qrs.index"),{event_id:a},{preserveState:!0,replace:!0})});const u=[{title:"Manajemen Event",href:route("admin.events.index")},{title:"QR Code Statis",href:route("admin.events.static-qrs.index")}];return(a,l)=>(y(),g(S,null,[w(b(Se),{title:"Manajemen QR Code Statis"}),w(Ve,{breadcrumbs:u},{default:$e(()=>[c("div",Xe,[c("div",Ye,[c("div",Ze,[l[4]||(l[4]=c("label",{for:"event-select",class:"block text-sm font-medium text-gray-700 dark:text-gray-300"},"Pilih Event untuk Dikelola",-1)),L(c("select",{"onUpdate:modelValue":l[0]||(l[0]=d=>r.value=d),id:"event-select",class:"mt-1 block w-full rounded-md border-gray-300 p-2 text-gray-900 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300"},[l[3]||(l[3]=c("option",{value:""},"-- Silakan Pilih Event --",-1)),(y(!0),g(S,null,H(a.events,d=>(y(),g("option",{key:d.id,value:d.id},C(d.name),9,et))),128))],512),[[Re,r.value]])]),a.selectedEvent?(y(),g("div",tt,[c("div",st,[c("h3",rt,"Buat QR Code Baru untuk: "+C(a.selectedEvent.name),1),c("form",{onSubmit:Ee(n,["prevent"]),class:"mt-4 flex items-end gap-4"},[c("div",ot,[l[5]||(l[5]=c("label",{for:"label",class:"mb-2 block text-sm font-medium"},"Label QR Code",-1)),L(c("input",{"onUpdate:modelValue":l[1]||(l[1]=d=>b(o).label=d),id:"label",type:"text",placeholder:"Contoh: Pintu Masuk Barat",class:"mt-1 block w-full rounded-md p-2 dark:bg-gray-700",required:""},null,512),[[Me,b(o).label]]),b(o).errors.label?(y(),g("p",nt,C(b(o).errors.label),1)):j("",!0)]),c("button",{type:"submit",disabled:b(o).processing,class:"inline-flex items-center gap-2 rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white"},[w(b(Ne),{class:"h-4 w-4"}),l[6]||(l[6]=F(" Buat "))],8,it)],32)]),c("div",at,[c("div",lt,[l[8]||(l[8]=c("h2",{class:"text-xl font-bold text-gray-800 dark:text-gray-200"},"Daftar QR Code",-1)),a.selectedEvent.static_qrs&&a.selectedEvent.static_qrs.length>0?(y(),g("button",{key:0,onClick:l[2]||(l[2]=d=>h(a.selectedEvent.static_qrs,a.selectedEvent)),class:"inline-flex items-center gap-2 rounded-md bg-blue-600 px-3 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-blue-700"},[w(b(Ae),{class:"h-4 w-4"}),l[7]||(l[7]=F(" Cetak Semua "))])):j("",!0)]),!a.selectedEvent.static_qrs||a.selectedEvent.static_qrs.length===0?(y(),g("div",dt," Belum ada QR Code dibuat. ")):(y(),g("div",ct,[(y(!0),g(S,null,H(a.selectedEvent.static_qrs,d=>(y(),g("div",{key:d.id,class:"flex flex-col items-center rounded-lg border p-4 dark:border-gray-700"},[c("h4",ft,C(d.label),1),c("canvas",{id:`qr-canvas-${d.id}`,onVnodeMounted:()=>i(d)},null,8,ut),c("p",mt,C(d.code),1),c("div",pt,[c("button",{onClick:k=>p(d,a.selectedEvent),class:"text-sm text-blue-600 hover:underline"},"Cetak",8,ht),c("button",{onClick:k=>f(d),class:"text-red-600 hover:text-red-500"},[w(b(Pe),{class:"h-4 w-4"})],8,gt)])]))),128))]))])])):(y(),g("div",yt,[w(b(Be),{class:"h-16 w-16 text-gray-400"}),l[9]||(l[9]=c("h3",{class:"mt-2 text-lg font-medium text-gray-900 dark:text-gray-100"},"Pilih Event untuk Memulai",-1)),l[10]||(l[10]=c("p",{class:"mt-1 text-sm text-gray-500"},"Pilih event dari dropdown di atas untuk mengelola QR Code statisnya.",-1))]))])])]),_:1})],64))}});export{Ht as default};
