<template>
	<div class="ui raised segment comment custom_segment_comment">
		<a class="avatar">
			<img :src="image">
		</a>
		<div class="content">
			<a v-if="isAdmin(user)" :href="FOSRouting.generate('app_admin_comments', {app_name: appName, user_username: user.username})" class="author">{{ comment.user.username }}</a>
			<span v-else class="author">{{ comment.user.username }}</span>
			<div class="metadata">
				<span class="date">{{ moment(comment.dtCreation.timestamp).format('DD/M/YYYY') }}</span>
			</div>
			<div class="text">{{ comment.content }}</div>
		</div>
	</div>
</template>

<script>
	import Moment from 'moment';

	export default {
		name: 'commentaryItem',
		props: [
				'image',
				'comment',
				'appName'
		],
		data () {
			return {
				user: window.environment.user,
				FOSRouting: window.environment.routing
			}
		},
		methods: {
			moment() {
				return Moment();
			},
			isAdmin(user) {
				return user.roles.findIndex(e => {
						return e == 'ROLE_ADMIN';
					});
			}
		}
	}
</script>