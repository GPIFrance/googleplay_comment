import Vue from "vue";
import Resource from "vue-resource";

Vue.use(Resource);

Vue.http.options.root = "/api";

export const EntityResource = Vue.resource("{entity}");
export const CommentResource = Vue.resource("comments{/appName}");