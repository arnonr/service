import Vue from 'vue'

// axios
import axios from 'axios'

let urlNew = '';
if(location.port  == '3000'){
    urlNew = 'http://localhost:3000/api/';
}else{
    if(location.hostname == 'localhost'){
        urlNew = 'http://localhost/service/api/';
    }else{
        urlNew = 'http://sci.kmutnb.ac.th/service/api/';
    }
}

const axiosIns = axios.create({
  // You can add your headers here
  // ================================
  baseURL: urlNew,
  // timeout: 1000,
  // headers: {'X-Custom-Header': 'foobar'}
})

Vue.prototype.$http = axiosIns

export default axiosIns
