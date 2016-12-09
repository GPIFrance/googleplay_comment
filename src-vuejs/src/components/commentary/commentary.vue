<template>
	<div>
		<h2 class="ui dividing header">Commentaires</h2>
		<div class="ui comments basic segment custom_comments_container" v-if="comments">
			<commentary-item v-for="comment in comments" :app-name="appName" :comment="comment" image="/images/businessman.svg"></commentary-item>
			<div v-if="loading" class="ui active dimmer">
				<div class="ui loader"></div>
			</div>
		</div>
		<h3 v-else class="ui center aligned header">Aucun commentaire</h3>
	</div>
</template>
<script>
	import * as Api from '../../api';
	import CommentaryItem from './commentaryItem.vue';

	export default {
		name: 'commentary',
		components: {
			CommentaryItem
		},
		props: ['appName'],
		data () {
			return {
				comments: [],
				loading: true
			}
		},
		mounted () {
			this.getComments();
			window.setInterval(() => {
				this.getComments();
			}, 10000);
		},
		methods: {
			getComments() {
				Api.CommentResource.get({appName: this.appName}).then(response => {
					response.json().then(jsonResponse => {
						this.comments = jsonResponse.data;
						this.loading = false;
					});
				}, response => {
					console.error(response);
					this.loading = false;
				});
			}
		}
	}
</script>