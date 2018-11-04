// system.js
// main module
//----------------------------------

import User from "../../models/user";

import { BOOTSTRAP } from "../mutation-types.js";

/*
 * state
 *
 */

const state = {
	user: {},
	current_role : ''
};

/*
* module getters
*/

const getters = {
	user: state => state.user,
	current_role: state => state.current_role
};

/*
* module mutations
*/

const mutations = {
  /**
   * Always called on initial page load, which occurs in DashboardLayout
   * broadcast data-ready event
   */
	[BOOTSTRAP](state, data) {
		state.user = data.user;
	}
};

/*
* module actions
*/

const actions = {
	bootstrap({ dispatch, commit }) {
		return User.bootstrap().then(user => {
			commit(BOOTSTRAP, user);
		});
	}
};

export default {
  state,
  getters,
  mutations,
  actions
};
