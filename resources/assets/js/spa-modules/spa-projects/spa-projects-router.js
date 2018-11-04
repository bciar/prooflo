import Vue from 'vue'
import VueRouter from 'vue-router'
import AuthService from '../../services/auth'
Vue.use(VueRouter)

import ProjectsContainer from './ProjectsContainer.vue'
import ProjectsList from './ProjectsList.vue'
import ProjectsCreate from './ProjectsCreate.vue'
import ProjectsFiles from './ProjectsFiles.vue'
import Proofer from './Proofer.vue'
const routes = [
    //GENERAL
    {
        path: '/projects', component: ProjectsContainer,
        children: [
            { path: '/', component: ProjectsList, name : 'projects_list'},
            { path: 'create', component: ProjectsCreate },
            {
                path: '/projects/files/:project_id/:revision_id',
                component: ProjectsFiles,
                name: 'upload_files_with_revision',
                props: true
            },
            {
                path: '/projects/files/:project_id',
                component: ProjectsFiles,
                name: 'upload_files',
                props: true
            }
        ]
    },

    //PROOFER FREELANCER
    {
        path: '/proofer/:project_id/:revision_id',
        component: Proofer,
        name: 'proofer',
        props: true,
        meta: { requiresAuth: true }
    },

    /*  //PROOFER CLIENT
     {
         path: '/proofer_guest/:project_id/:revision_id/:project_hash',
         component: Proofer,
         name: 'proofer_guest',
         props: true,
         meta: { requiresAuth: true }
     } */
]

const router = new VueRouter({
    routes,
    mode: 'history',
    linkActiveClass: 'active'
})

/* router.beforeEach((to, from, next) => {
    // If the next route is requires user to be Logged IN
    if (to.matched.some(m => m.meta.requiresAuth)) {
        return AuthService.check().then(authenticated => {
            if (!authenticated) {
                window.location.href = '/login';
            } else {
                return next()
            }
        })
    }
    return next()
}); */

export default router