const r={containerSelector:"[data-tab-container]",singleContainerSelector:'[data-tab-container=":id"]',headerSelector:"[data-tab-header]",buttonSelector:"[data-tab-button]",singleButtonSelector:'[data-tab-button=":id"]',contentSelector:"[data-tab-content]",singleContentSelector:'[data-tab-content=":for"]',activeClassList:["fd-bg-zinc-100","fd-font-medium"],buttonTemplateSelector:"template[data-tab-button-template]",contentTemplateSelector:"template[data-tab-content-template]",buttons:t=>t.querySelectorAll(r.buttonSelector),contents:t=>t.querySelectorAll(r.contentSelector),content:(t,e)=>(e.startsWith("tab-")||(e=`tab-${e}`),t.querySelector(r.singleContentSelector.replace(":for",e))),listenContainer:t=>{t.querySelectorAll(r.buttonSelector).forEach(n=>{r.listenButton(n)})},listenButton:t=>{t.addEventListener("click",e=>{e.preventDefault(),r.select(t)})},select:t=>{const e=t.closest(r.containerSelector);r.buttons(e).forEach(o=>{o.classList.remove(...r.activeClassList)}),r.contents(e).forEach(o=>{o.style.display="none"}),r.content(e,t.dataset.tabButton).style.removeProperty("display"),t.classList.add(...r.activeClassList)},create:t=>{const e=document.querySelector(r.singleContainerSelector.replace(":id",t.container)),n=e.querySelector(r.headerSelector),o=n.querySelector(r.singleButtonSelector.replace(":id",`tab-${t.id}`));if(o)return{button:o,content:r.content(e,t.id)};const c=document.importNode(document.querySelector(r.buttonTemplateSelector).cloneNode(!0).content,!0),a=document.importNode(document.querySelector(r.contentTemplateSelector).cloneNode(!0).content,!0),s=c.querySelector(r.buttonSelector),d=a.querySelector(r.contentSelector);s.textContent=t.title,s.setAttribute("data-template-tab","data-template-tab"),s.setAttribute("data-tab-button",s.getAttribute("data-tab-button").replace(":id",t.id)),d.innerHTML=t.content,d.setAttribute("data-template-tab","data-template-tab"),d.setAttribute("data-tab-content",d.getAttribute("data-tab-content").replace(":for",t.id)),c.childNodes.forEach(u=>{n.appendChild(u.cloneNode(!0))}),a.childNodes.forEach(u=>{e.appendChild(u.cloneNode(!0))});const i=e.querySelectorAll(r.buttonSelector),S=i[i.length-1];return r.listenButton(S),{button:S,content:r.content(e,t.id)}},printErrors:t=>{t.querySelectorAll(r.contentSelector).forEach(n=>{var a;if(n.querySelectorAll(".fd-has-error").length<=0)return;const c=t.querySelector(r.singleButtonSelector.replace(":id",n.dataset.tabContent));(a=c==null?void 0:c.classList)==null||a.add("fd-has-error")})}};document.querySelectorAll(r.containerSelector).forEach(t=>{r.listenContainer(t),setTimeout(()=>{r.printErrors(t)},250)});window.Feadmin.Tab=r;const l={containerSelector:"[data-conditional-field-item]",init(t){const e=l.parseConditions(t);e&&e.forEach(n=>{const{key:o,value:c,operator:a}=n,s=document.getElementById(o);if(!s)return;s.addEventListener("change",()=>{const i=l.compare(s.value,c,a);l.toggle(t,i)});const d=l.compare(s.value,c,a);l.toggle(t,d)})},listen(t){t.querySelectorAll(l.containerSelector).forEach(e=>{l.init(e)})},toggle(t,e){e?t.classList.remove("fd-hidden"):t.classList.add("fd-hidden")},compare(t,e,n){switch(n){case"===":return t===e;case"!==":return t!==e;case"==":return t==e;case"!=":return t!=e;case">":return t>e;case">=":return t>=e;case"<":return t<e;case"<=":return t<=e;case"in":return e.includes(t);case"not_in":return!e.includes(t);case"between":return t>=e[0]&&t<=e[1];case"not_between":return t<e[0]||t>e[1];case"contains":return t.includes(e);case"not_contains":return!t.includes(e);case"starts_with":return t.startsWith(e);case"ends_with":return t.endsWith(e);case"regex":return new RegExp(e).test(t);case"not_regex":return!new RegExp(e).test(t);default:return!1}},parseConditions(t){const e=t.dataset.conditionalFieldItem;if(e)return JSON.parse(e)}};l.listen(document);export{l as C,r as T};