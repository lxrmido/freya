var util = {
    strCode : "0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ",
    randKey : function(length){
        var s = '';
        var i;
        for(i = 0; i < length; i ++){
            s += this.randChar();
        }
        return s;
    },
    randChar : function(){
        return this.strCode[parseInt(Math.random() * this.strCode.length)];
    },
    fromUnicode : function(s){
        s = s.replace(/u([\w]{4})/g, '\\u$1');
        try{
            eval("s='"+s+"'");
        }catch(e){
            console.log(s)
        }
        return s;
    }
};

var G = (function(window){
    
    var G = function(){
        
    }
    
    G.modURL = C.modURL;

    G.go = function(url, open){
        if(open){
            window.open(url);
        }else{
            document.location.href = url;
        }
    };

    G.submit = function(url, data){
        var form = document.createElement('form');
        var ip, i;
        for(i in data){
            ip = document.createElement('input');
            ip.type  = 'hidden';
            ip.name  = i;
            ip.value = data[i];
            form.appendChild(ip);
        }
        form.method = 'post';
        form.action = url;
        document.body.appendChild(form);
        form.submit();
    };

    G.upload = function(url, arg, file, key_name, progress_callback, loaded_callback){
        var formData = new FormData(),
            oXHR     = new XMLHttpRequest();
        var i;

        oXHR.upload.addEventListener('progress', progress_callback, false);
        oXHR.addEventListener('load', loaded_callback, false);

        for(i in arg){
            formData.append(i, arg[i]);
        }

        formData.append(key_name, file);
        
        oXHR.open('POST', G.callURL(url));
        oXHR.send(formData);
    };
    
    G.call = function(a, b, c, d){
        var url, arg, func_ok, func_er;
        if(arguments.length == 4){
            url = a;
            arg = b;
            func_ok = c;
            func_er = d;
        }else if(arguments.length == 3){
            url = a;
            if(typeof b == "function"){
                func_ok = b;
                func_er = c;
            }else{
                arg = b;
                func_ok = c;
            }
        }else if(arguments.length == 2){
            url = a;
            if(typeof b == "function"){
                func_ok = b;
            }else{
                arg = b;
            }
        }else{
            url = a;
        }
        arg = arg || {};

        var xhr = new XMLHttpRequest();
        xhr.handleerror = function(d){
            console.log(d)
            if(!d){
                console.error("Connetction error.");
            }else if(typeof d == "string"){
                console.error("XHRParseError");
                console.log([a, b, c, d]);
                console.log(d);
            }else if(d.code !== undefined){
                func_er && func_er(d.code, d.message);
            }else{
                console.error("XHRIllegal");
                console.log([a, b, c, d]);
                console.log(d);
            }
        }
        xhr.onreadystatechange = function(){
            var d;
            if(this.readyState == 4){
                if(this.status == 200){
                    d = this.responseText;
                    try{
                        d = JSON.parse(d);
                    }catch(e){
                        console.error("JSONParseError or CallbackError");
                        console.error(e);
                        this.handleerror(d);
                        return;
                    }
                    if(d.code && d.code > 0){
                        func_ok && func_ok(d.code, d.args)
                    }else{
                        this.handleerror(d);
                    }
                }else{
                    console.log(this.status);
                    this.handleerror();
                }
            }
        }
        xhr.open("POST", G.callURL(url), true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        xhr.send(G.serialize(arg));
    };

    G.serialize = function(o){
        var a = [], i;
        for(i in o){
            a.push(i + '=' + encodeURIComponent(o[i]));
        }
        return a.join('&');
    };

    G.error = function(str){
        if(window.ui){
            ui.notify(str, true) || ui.alert(str);
        }else{
            alert(str);
        }
    };

    G.url = function(c, a, args){
        var url, i, fi;
        if(_RG.rewrite){
            url = _RG.url_root + '/' + c + '/' + a;
            fi = true;
        }else{
            url = _RG.url_root + '/?c=' + c + '&a=' + a;
            fi = false;
        }
        if(args){
            for(i in args){
                if(fi){
                    fi = false;
                    url += '?' + i + '=' + encodeURIComponent(args[i]);
                }else{
                    url += '&' + i + '=' + encodeURIComponent(args[i]);
                }
            }
        }
        return url;
    };

    G.callURL = function(url){
        return G.url('core', 'call', {
            _m : url
        });
    };

    (function(){
        var d = document.createElement('div');
        var to_test = ['t', 'webkitT', 'mozT'];
        var i;
        for(i = 0; i < to_test.length; i ++){
            if((to_test[i] + 'ransform') in d.style){
                G.cssPrefix = to_test[i];
                return;
            }
        }
        if(!('dataset' in document.documentElement)){
            document.location.href = './?c=core&a=browser';
        }
    })();
    
    return G;
    
})(window);
