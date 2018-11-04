<template>
	<div v-loading.fullscreen.lock="fullscreenLoading">
		<el-row :gutter="10">
			<div class="project-list">
				<el-col v-show="!current_rol || (current_rol && current_rol != 'client' && listing == 'all')" :xs="24" :sm="6" :md="8" :lg="6" :xl="4">
					<router-link to="/projects/create" class="add-project-block" style="text-decoration: none">
						<i class="el-icon-plus color-theme"></i>
						<span>New Project</span>
					</router-link>
				</el-col>
				<el-col :xs="24" :sm="6" :md="8" :lg="6" :xl="4" v-for="(project, index, key) in current_projects" :key="key">
					<div class="w3-hover-shadow w3-center" style="margin-bottom: 10px">
						<div @click="openProofer(project.id, project.active_revision.id)" style="cursor: pointer">
							<div v-if="project.active_revision.proofs.length" class="img-thumb" v-bind:style="{ 'background-image': 'url(' + '/storage/' + project.active_revision.proofs[0].project_files.path + ')'}">
								<!-- <div>
								<span style="float: left; padding-left: 10px">
									<el-button :id="'cardMenu'+key" @click="showCardMenu(index)" type="text" size="small" style="padding-top: 5px" title="Issues">
										<i :id="'cardIcon' + index" class="el-icon-more icon-center" style="font-size: 24px; color: #faf9f9"></i>
										<div :id="'myDropdown' + index" class="dropdown-content">
											<a href="#home" style="padding: 10px">Edit</a>
											<a href="#about">Details</a>
											<a href="#contact">Delete</a>
										</div>
									</el-button>
								</span>
								<span style="float: right; padding-right: 10px">
									sdsds
								</span>
    							</div>-->
							</div>
							<div v-else class="img-thumb" v-bind:style="{ 'background-image': 'url(' + '/img/placeholder.jpg' + ')' }">
								<!-- <div>
								<span style="float: left; padding-left: 10px">
									<el-button :id="'cardMenu'+key" @click="showCardMenu(key)" type="text" size="small" style="padding-top: 5px" title="Issues">
										<i :id="'cardIcon' + index" class="el-icon-more icon-center" style="font-size: 24px; color: #faf9f9"></i>
										<div :id="'myDropdown' + index" class="dropdown-content">
											<a href="#home">Home</a>
											<a href="#about">About</a>
											<a href="#contact">Contact</a>
										</div>
									</el-button>
								</span>
								<span style="float: right; padding-right: 10px">
									sdsds
								</span>
							</div> -->
							</div>
							<div class="w3-container text-left">
								<div class="project-name" style="margin-top: 10px">
									<span style="margin-bottom: 2px; font-size: 16px;">
										<strong>{{ project.name }}</strong>
									</span>
								</div>
								<div class="company">{{ project.company }}</div>
							</div>
						</div>

						<div class="w3-container w3-center" style="margin-top: 10px; padding-left: 5px; padding-right: 5px">
							<template v-if="project.active_revision.proofs.length > 0">
								<el-row style="padding-bottom: 15px">
									<el-col :md="6" style="padding: 5px">
										<!-- <el-badge :value="getTotalIssues(project.id)" class="item"> -->
										<el-button type="text" @click="openProofer(project.id, project.active_revision.id)" size="small" style="padding-top: 5px"
										 title="Issues">
											<i class="fa fa-comment icon-center" style="font-size: 30px; color: #fc7c7c"></i>
											<el-badge :value="getTotalIssues(project.id)" class="item inner-badge" style="right: 31px; top: -2px;"></el-badge>
										</el-button>
										<!-- </el-badge> -->
									</el-col>
									<el-col :md="6" style="padding: 5px">
										<el-tag size="small" style="background-color: #80B4A0; border-radius: 30px; margin-top: 10px; margin-right: 10px; color: #fff"
										 title="Current revision">
											<b>R - {{project.active_revision.version}}</b>
										</el-tag>
									</el-col>
									<el-col :md="6" style="padding: 5px">
										<button v-show="current_rol == 'freelancer'" class="edition-btn" @click="sendProject(project.id)" style="background-color: #f3f1f1"
										 title="Send by email">
											<i class="el-icon-message icon-center" style="font-size: 20px; color: #797878"></i>
										</button>
									</el-col>
									<el-col :md="6" style="padding: 5px">
										<button class="edition-btn" @click="openProofer(project.id, project.active_revision.id)" style="background-color: #f3f1f1"
										 title="Open in proofer">
											<i class="el-icon-edit icon-center" style="font-size: 20px; color: #797878"></i>
										</button>
									</el-col>
									<!-- <el-col :md="5" style="padding: 5px">
										<button class="edition-btn" @click="" style="background-color: #f3f1f1" title="Project info">
											<i class="el-icon-info icon-center" style="font-size: 20px ;color: #797878"></i>
										</button>
									</el-col> -->
								</el-row>
							</template>
							<template v-else>
								<el-row style="padding-bottom: 15px; margin-top: 5px">
									<el-col :xs="24" style="padding: 5px">
										<button v-show="current_rol == 'freelancer'" class="edition-btn" @click="addPictures(project.id, project.active_revision.id)"
										 style="background-color: #fc7c7c; border: 0px" title="Add proofs">
											<i class="el-icon-plus icon-center" style="font-size: 20px ;color: #fff; font-weight: bold"></i>
										</button>
									</el-col>
								</el-row>
							</template>
							<!-- <el-button v-if="project.image" @click="openRevisions(project.id, project.name)" type="primary" size="mini" round>Open in proofer</el-button>
								<el-button v-if="project.active_revision.proofs.length > 0" @click="openProofer(project.id, project.active_revision.id)"
								 type="primary" size="mini">Open in proofer</el-button>
								<el-button v-else @click="addPictures(project.id, project.active_revision.id)" type="warning" size="mini">Add proofs</el-button>
								<el-button v-if="project.status != 'approved' && project.active_revision.proofs.length > 0 && current_rol == 'freelancer'" @click="sendProject(project.id)"
								 icon="el-icon-message" type="warning" size="mini">Send</el-button> -->
						</div>
					</div>
					<!--<div class="project-block">
						<div v-if="current_projects.length > 0" class="image" v-bind:style="{ 'background-image': 'url(' + '/storage/' + project.active_revision.proofs[0].project_files.path + ')'}">
						</div>
						<div v-else class="image" v-bind:style="{ 'background-image': 'url(' + '/assets/front/img/placeholder.jpg' + ')' }">
						</div>
						<div class="project-details">
							<div>
								<span class="project-name">{{ project.name }}</span>
								<span class="company" style="float: right">{{ project.company }}</span>
							</div>
							<hr>
							<div>Active revision: {{project.active_revision.version}}</div>
							<hr>
							<div style="text-align: center">
								 <el-button v-if="project.image" @click="openRevisions(project.id, project.name)" type="primary" size="mini" round>Open in proofer</el-button>
								<el-button v-if="project.active_revision.proofs.length > 0" @click="openProofer(project.id, project.active_revision.id)" type="primary"
								 size="mini">Open in proofer</el-button>
								<el-button v-else @click="addPictures(project.id, project.active_revision.id)" type="warning" size="mini">Add proofs</el-button>
								<el-button v-if="project.status != 'approved' && project.active_revision.proofs.length > 0" @click="sendProject(project.id)"
								 icon="el-icon-message" type="warning" size="mini">Send</el-button>
							</div>
						</div>
					</div> -->
				</el-col>

			</div>
		</el-row>
	</div>
</template>

<script>
	import { mapActions, mapGetters } from "vuex";
	export default {
		props: ["user"],
		data() {
			return {
				current_projects: [],
				user_type: "freelancer",
				fullscreenLoading: false,
				current_rol: ''
			};
		},
		methods: {
			addPictures(project_id, revision_id) {
				this.$router.push({ name: 'upload_files', params: { project_id: project_id, revision_id: revision_id } })
			},
			openProofer(project_id, revision_id) {
				this.$router.push({ name: 'proofer', params: { project_id: project_id, revision_id: revision_id } })
			},
			sendProject(project_id) {
				var self = this;
				this.$confirm('Are you ready to send this revision to the client?', 'Warning', {
					confirmButtonText: 'Confirm',
					cancelButtonText: 'Cancel',
					type: 'success',
					title: ''
				}).then(() => {
					this.fullscreenLoading = true
					axios.get('/api/projects/send_project/' + project_id + '/' + this.user_type).then(response => {
						this.fullscreenLoading = false
						this.loadProjects();
						toastr['success'](response.data.message, 'Success')
						this.fullscreenLoading = false
					}).catch(error => {
						this.fullscreenLoading = false
						console.log(error.message)
					});
				}).catch(() => {
					console.log('canceled')
				});
			},
			showCardMenu(index) {
				$("#myDropdown" + index).toggleClass("show");
			},
			hideCardMenu(event) {
				var target = event.target;
				for (var i = 0; i < this.current_projects.length; i++) {
					var str = "cardIcon" + i;
					if (target.id !== str) {
						$("#myDropdown" + i).removeClass("show");
					}
				}
			},
			getTotalIssues(project_id) {
				var issues_count = 0;
				if (this.current_projects.length > 0) {
					for (var i in this.current_projects) {
						if (this.current_projects[i].id == project_id) {
							if (this.current_projects[i].active_revision.proofs.length > 0) {
								for (var j in this.current_projects[i].active_revision.proofs) {
									if (this.current_projects[i].active_revision.proofs[j].issues.length > 0) {
										for (var k in this.current_projects[i].active_revision.proofs[j].issues) {
											issues_count++;
										}
									}
								}
							}
						}
					}
				}
				return issues_count;

			},
			...mapActions(["loadProjects"])
		},
		computed: {
			showByStatus() {
				return this.$store.state.projects.show_project_by_status;
			},
			...mapGetters(["projects", "in_progress", "on_revision", "approved", "listing"])
		},
		watch: {
			projects() {
				/* console.log(this.projects); */
				this.fullscreenLoading = false;
				this.current_projects = this.projects;
				if (this.current_projects.length > 0) {
					for (var i in this.current_projects) {
						if (this.current_projects[i].role == 'freelancer') {
							this.current_rol = this.current_projects[i].role;
						} else if (this.current_projects[i].role == 'client') {
							this.current_rol = this.current_projects[i].role;
						}
						return;
					}
				}
			},
			showByStatus() {
				if (this.showByStatus == 0) {
					this.current_projects = this.projects;
				} else if (this.showByStatus == 1) {
					this.current_projects = this.in_progress;
				} else if (this.showByStatus == 2) {
					this.current_projects = this.on_revision;
				} else if (this.showByStatus == 3) {
					this.current_projects = this.approved;
				}
			}
		},
		mounted() {
			var self = this;
			this.$nextTick(function () {
				window.onclick = function (event) {
					self.hideCardMenu(event);
				}
			});
			this.fullscreenLoading = true;
		},
		created() {
			/* this.fullscreenLoading = true;
			axios.get("/api/auth/getCurrentRole/" + this.project_id)
				.then(response => {
					if (response.data.status == 1) {
						this.current_rol = response.data.data;
						this.fullscreenLoading = false;
					} else {
						this.handle_error(response.data.errors);
					}
				})
				.catch(error => {
					self.sendLoading = false;
				}); */
		},
		updated() {

		},
		afterRouteUpdate: function (to, from, next) {
			next();
		}
	};
</script>
<style>
	.add-project-block {
		text-decoration: none;
		border: 2px dashed #bdbbbb;
		border-radius: 10px;
	}

	.add-project-block:hover {
		border: 2px dashed #949292;
		background-color: rgb(245, 243, 243);
	}

	hr {
		border: 1px dashed rgb(236, 234, 234);
	}

	.el-menu {
		border-right: 0px !important;
	}

	.img-thumb {
		width: auto;
		height: 120px;
		background-repeat: no-repeat;
		background-position: center center;
		background-size: cover;
		border-radius: 5px;
	}

	.w3-hover-shadow {
		border: 1px solid #c5c3c3 !important;
		border-radius: 5px;
	}

	.edition-btn {
		width: 40px;
		height: 40px;
		border-radius: 50%;
		text-align: center;
		padding-left: 6px;
		padding-bottom: 10px;
		font-size: 22px;
		border: 1px solid #cacbcc;
		color: #626364;
		outline: none;
		background-color: #fff;
	}

	.dropbtn {
		color: white;
		cursor: pointer;
	}

	.dropbtn:hover,
	.dropbtn:focus {
		background-color: #2980B9;
	}

	.dropdown {
		position: relative;
		display: inline-block;
	}

	.dropdown-content {
		display: none;
		position: absolute;
		background-color: #f1f1f1;
		min-width: 160px;
		overflow: auto;
		box-shadow: 0px 8px 16px 0px rgba(0, 0, 0, 0.2);
		z-index: 1;
	}

	.dropdown-content a {
		color: black;
		padding: 12px 16px;
		text-decoration: none;
		display: block;
	}

	.dropdown a:hover {
		background-color: #ddd
	}

	.show {
		display: block;
	}

	.inner-badge>.el-badge__content {
		background-color: rgb(252, 124, 124);
		border: 0px;
		color: #fff;
	}

	.navbar {
		background-color: #545c64;
	}
</style>