<template>
  <div v-loading.fullscreen.lock="fullscreenLoading" class="container-fluid" style="padding-left: 0px; padding-right: 0px">
    <el-container style="margin-top: 70px;">
      <el-aside style="background-color: #545c64; width:15%">
        <el-menu default-active="2" class="el-menu-vertical-demo" background-color="#545c64" text-color="#fff" active-text-color="#ffd04b">
          <el-menu-item index="2" @click="load('/')" :class="{'active_menu' : active_list}">
            <i class="el-icon-tickets icon-projects"></i>
            <span style="color: #fff; font-size: 16px">Project list</span>
            <el-badge v-if="projects" :value="projects.length"></el-badge>
          </el-menu-item>
          <el-menu-item index="2" @click="load('progress')" :class="{'active_menu' : active_progress}">
            <i class="el-icon-time icon-progress"></i>
            <span style="color: #fff; font-size: 16px">In Progress</span>
            <el-badge v-if="in_progress" :value="in_progress.length"></el-badge>
          </el-menu-item>
          <el-menu-item index="2" @click="load('revision')" :class="{'active_menu' : active_revision}">
            <i class="el-icon-edit icon-revision"></i>
            <span style="color: #fff; font-size: 16px">In Review</span>
            <el-badge v-if="on_revision" :value="on_revision.length"></el-badge>
          </el-menu-item>
          <el-menu-item index="2" @click="load('approved')" :class="{'active_menu' : active_approved}">
            <i class="el-icon-check icon-approved"></i>
            <span style="color: #fff; font-size: 16px">Approved</span>
            <el-badge v-if="approved" :value="approved.length"></el-badge>
          </el-menu-item>
        </el-menu>
      </el-aside>
      <el-main style="width:85%; min-height: 100vh; left: 0;">
        <router-view></router-view>
      </el-main>
    </el-container>
  </div>
</template>

<script>
  import { mapActions, mapGetters } from "vuex";
  export default {
    props: ["user"],
    data() {
      return {
        active_list: true,
        active_progress: false,
        active_revision: false,
        active_approved: false,

        fullscreenLoading: false
      };
    },
    methods: {
      load(status) {
        this.$router.push({ path: '/projects' });
        switch (status) {
          case "/":
            this.active_list = true;
            this.active_progress = false;
            this.active_revision = false;
            this.active_approved = false;
            this.$store.commit("set_show_project_by_status", 0);
            this.$store.commit("set_current_project_listing", 'all');
            break;
          case "progress":
            this.active_list = false;
            this.active_progress = true;
            this.active_revision = false;
            this.active_approved = false;
            this.$store.commit("set_show_project_by_status", 1);
            this.$store.commit("set_current_project_listing", 'progress');

            break;
          case "revision":
            this.active_list = false;
            this.active_progress = false;
            this.active_revision = true;
            this.active_approved = false;
            this.$store.commit("set_show_project_by_status", 2);
            this.$store.commit("set_current_project_listing", 'revision');
            break;
          case "approved":
            this.active_list = false;
            this.active_progress = false;
            this.active_revision = false;
            this.active_approved = true;
            this.$store.commit("set_show_project_by_status", 3);
            this.$store.commit("set_current_project_listing", 'approved');
            break;
        }
      },
      ...mapActions([
        'loadProjects'
      ])
      /*  ...mapActions([
         'bootstrap',
       ]) */
    },
    computed: {
      ...mapGetters(["projects", "in_progress", "on_revision", "approved"])
    },
    mounted() {
      this.$nextTick(() => {
        /* this.bootstrap() */
      })
    },
    created() {
      this.loadProjects();
    },
    beforeRouteUpdate: function (to, from, next) {
      this.loadProjects();
      next();
    }
  };
</script>
<style>
  .el-menu {
    border-right: 0px !important;
  }

  .with-navbar {
    padding-top: 0px !important;
  }

  .navbar {
    border-bottom: 0px !important;
  }

  .el-badge__content {
    background-color: #fff;
    color: black;
    font-weight: bold;
  }

  .active_menu {
    background-color: #444a50 !important;
  }

  .el-menu-item:focus,
  .el-menu-item:hover {
    background-color: #444a50 !important;
  }

  .el-aside {
    position: fixed;
    height: 100%;
  }

  .el-main {
    margin-left: 15%;
  }
</style>