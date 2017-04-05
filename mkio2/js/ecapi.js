
// base ECAPI class that can get from a URI


ECAPI = function(baseurl) {
  this.baseurl = baseurl;
};

ECAPI.prototype.get = function(type, entity, callback, params){
    var url = this.baseurl+type;
    if (entity!=null) url += '/'+entity;
    jQuery.ajax({
       url: url,
     }).done(function(data) {callback(data, params);})
};


