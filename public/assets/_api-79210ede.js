function c(r,e={}){e.headers=e.headers||{},e.headers["Content-Type"]="application/json",e.headers.Accept="application/json",e.headers["X-CSRF-TOKEN"]=document.querySelector('meta[name="csrf-token"]').content,e.credentials="include";let a;return r.startsWith("http")?a=r:(r=r.replace(/^\/+/g,""),a=`${window.Feadmin.API.baseUrl}/${r}`),fetch(a,e)}export{c as a};