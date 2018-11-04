// system.js
// main module
//----------------------------------

import Project from "../../models/project";

import { PROJECTS_LOAD } from "../mutation-types.js";

/*
 * state
 *
 */

const state = {
	projects: {},
	in_progress: {},
	on_revision: {},
	approved: {},
	current_proofs_state: '',
	show_project_by_status: 0,
	current_project_listing: 'all'
};

/*
* module getters
*/

const getters = {
	projects: state => state.projects,
	current_proofs_state: state => state.current_proofs_state,
	in_progress: state => state.in_progress,
	on_revision: state => state.on_revision,
	approved: state => state.approved,
	listing: state => state.current_project_listing
};

/*
* module mutations
*/

const mutations = {
	/**
	 * Always called on initial page load, which occurs in DashboardLayout
	 * broadcast data-ready event
	 */
	[PROJECTS_LOAD](state, data) {
		if (data) {
			state.projects = data;
			state.in_progress = data.filter(project => project.active_revision.status_revision == 'progress');
			state.on_revision = data.filter(project => project.active_revision.status_revision == 'revision');
			state.approved = data.filter(project => project.active_revision.status_revision == 'approved');
		}
	},

	SAVE_CURRENT_PROOF_DATA(state, data) {
		state.current_proofs_state = data;

	},

	RESET_CURRENT_PROOF_DATA(state) {
		state.current_proofs_state = [];
	},

	SAVE_CURRENT_PROJECT_NAME(state, data) {
		state.current_project_name = data;
	},

	SAVE_PROGRESS_PROJECT(state, data) {
		state.in_progress_projects = data;
	},

	SAVE_REVISION_PROJECTS(state, data) {
		state.on_revison_projects = data;
	},

	SAVE_APPROVED_PROJECTS(state, data) {
		state.approved_projects = data;
	},

	set_show_project_by_status(state, data) {
		state.show_project_by_status = data;
	},

	set_current_project_listing(state, data) {
		state.current_project_listing = data;
	}

};

/*
* module actions
*/

const actions = {

	loadProjects({ dispatch, commit }) {
		return Project.list().then(projects => {
			commit(PROJECTS_LOAD, Object.values(projects));
		});
	}
};

export default {
	state,
	getters,
	mutations,
	actions
};
