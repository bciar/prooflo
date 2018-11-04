<template>
    <div v-loading.fullscreen.lock="fullscreenLoading" style="margin-top: 0px">
        <div style="margin-bottom: 0px; padding-left: 0px; min-height: 65px; background-color: #fff; border-bottom: 1px solid rgb(219, 218, 218); position: fixed; width: 100%; z-index: 200; top : 0px">
            <el-row style="margin-bottom: 0px">
                <el-col :sm="1" style="height: 100%">
                    <div class="grid-content bg-back-btn" @click="goBack()" style="cursor: pointer">
                        <i class="el-icon-back" style="font-size: 30px; font-weight: 900; color: #5a5555af; margin-left: 25%; margin-top: 20%"></i>
                    </div>
                </el-col>
                <el-col :sm="3">
                    <el-select @change="loadRevisionById()" v-model="value" placeholder="version" style="width: 80px; margin-top: 5%; margin-left: 5%;">
                        <el-option v-for="(item, key, index) in versions" :key=key :label="item.label" :value="item.id">
                        </el-option>
                    </el-select>
                    <!-- <el-radio-group size="mini" v-model="showSideBar" style="margin-left: 5px">
                        <el-radio-button :label="false">|</el-radio-button>
                        <el-radio-button :label="true">|</el-radio-button>|| current_rol == 'collaborator'
                    </el-radio-group> -->
                </el-col>
                <el-col :sm="7" :offset="6">
                    <div style="margin-top: 10px">
                        <el-button v-if="current_rol == 'freelancer'" style="background-color: #fa5555; color: #fff; font-weight: bold; border: 0px"
                            @click="sendRevision()" type="primary">Send back for revision
                        </el-button>
                        <el-button v-if="current_rol == 'client'" style="background-color: #fa5555; color: #fff; font-weight: bold; border: 0px"
                            @click="showSubmitOptionsModal()" type="primary">Submit corrections
                        </el-button>
                        <el-button v-if="current_rol == 'client'" style="background-color: #e0dfdf; color: rgb(87, 86, 86); font-weight: bold; border: 0px"
                            @click="showApproveDesignModal()" type="primary">Approve design
                        </el-button>
                    </div>
                </el-col>
                <el-col :sm="7">
                    <div class="pull-right" style="margin-top: 10px; margin-right: 10px;">
                        <ul class="collaborators" style="text-decoration: none">
                            <li v-for="item in collaborators" style="margin-right: 5px">
                                <img :src="item.photo_url" class="spark-nav-profile-photo m-r-xs">
                            </li>
                            <li v-if="current_rol == 'freelancer' || current_role == 'client'">
                                <el-button class="is-circle"
                                           @click="showInviteCollaboratorModal()" type="danger" icon="el-icon-plus">
                                </el-button>
                            </li>
                        </ul>
                    </div>
                </el-col>
            </el-row>
        </div>
        <div id="buttons_bar" class="w3-white w3-card" style="padding-top: 10px; padding-bottom: 10px; width: 100%; position: fixed; z-index: 300; height: 60px">
            <div class="w3-container">
                <el-row type="flex">
                    <el-col :sm="9" :md="9" :offset="5">
                        <div style="margin-left: 20px">
                        </div>
                    </el-col>
                    <el-col :sm="1" :md="1" style="text-align: center">
                        <button v-show="current_rol == 'client'" :class="{'edition-btn-active' : pen_active, 'edition-btn' : !pen_active}" @click="activate_pen()"
                            style="float: right">
                            <i class="el-icon-edit icon-center" style="font-size: 20px"></i>
                        </button>
                    </el-col>
                    <!-- <el-col :sm="1" :md="1">
                        <button v-show="current_rol == 'client'" :class="{'edition-btn-active' : remove_active, 'edition-btn' : !remove_active}"
                            @click="deleteIssue()" :disabled="disabled_remove">
                            <i class="el-icon-delete icon-center"></i>
                        </button>
                    </el-col> -->
                    <el-col :sm="1" :md="1">
                        <button :class="{'edition-btn-active' : zoom_out_active, 'edition-btn' : !zoom_out_active}" @mousedown="activate_zoom_in()"
                            style="float: right" @mouseup="deactivate_zoom_in()">
                            <i class="el-icon-zoom-out icon-center" style="font-size: 20px"></i>
                        </button>
                    </el-col>
                    <el-col :sm="2" :md="2">
                        <el-slider v-model="zoom_level" :min="0.1" :max="10" :step="0.01" :height="'2px'" :show-tooltip="false" style="margin-top: 2px !important">
                        </el-slider>
                    </el-col>
                    <el-col :sm="1" :md="1">
                        <button :class="{'edition-btn-active' : zoom_in_active, 'edition-btn' : !zoom_in_active}" @mousedown="activate_zoom_out()"
                            style="float: left" @mouseup="deactivate_zoom_out()">
                            <i class="el-icon-zoom-in icon-center" style="font-size: 20px"></i>
                        </button>
                    </el-col>
                    <!--   <el-col :sm="1">

                    </el-col> -->
                </el-row>
            </div>
        </div>
        <div style="margin-top: 63px !important">
            <div id="leftSidebar" class="w3-sidebar w3-bar-block w3-card w3-animate-left" style="display:none; padding: 10px">
                <el-card :id="'proof_'+key" v-for="(proof, key, index) in proofs" :key=key :body-style="{ padding: '0px'}" @click.native="loadThumb(key)"
                    :style="{'background-image' : 'url('+ proof.picture_url +')'}" style="background-size: cover; background-repeat: no-repeat; border-radius: 5px; margin-bottom: 5px; min-height: 180px;"
                    :class="{'regular-thumb' : !proof.active_thumb, 'active-thumb' : proof.active_thumb}">
                    <el-col :xs="4" :sm="2" :md="2" :lg="3" :class="{'regular-thumb-number' : !proof.active_thumb, 'active-thumb-number' : proof.active_thumb}">
                        <span style="font-size: 16px; color: #fff">
                            <b>{{key+1}}</b>
                        </span>
                    </el-col>
                </el-card>
            </div>
            <div id="rightSidebar" class="w3-sidebar w3-bar-block w3-card" style="width:20%; right:0;">
                <el-row>
                    <!-- <el-col :xs="24"> -->
                    <!-- <div class="grid-content side-menu-right"> -->
                    <!-- <transition-group name="el-fade-in"> -->
                    <div :key="1" v-show="!showIssueDetails">
                        <el-row type="flex" justify="end">
                            <el-col>
                                <div v-for="(_current_issue, key, index ) in current_proof.issues" :key=key :class="{'current_issue_active' : _current_issue.active_issue, 'current_issue_normal' : !_current_issue.active_issue, 'current_issue_solved' : _current_issue.status == 'solved'}">
                                    <div @click="IssueDetails(_current_issue.group)" style="cursor: pointer;">
                                        <div v-if="_current_issue.status == 'solved'" style="color: #009688; text-align: center; font-weight: bold; padding: 5px">This issue has been resolved</div>
                                        <div v-else style="color: #da4545;; text-align: center; font-weight: bold; padding: 5px">This issue is pending</div>
                                        <el-row style="padding-top: 3%; margin-bottom: 10px">
                                            <el-col :xs="5" :md="5">
                                                <div style="padding: 10px">
                                                    <img v-if="_current_issue.user" :src="_current_issue.user.photo_url" class="img-circle special-img" style="width: 50px;height: 50px;padding: 2px;border-style: solid;border-color: #a5a3a3;border-width: 1px;">
                                                </div>
                                            </el-col>
                                            <el-col :xs="16" :md="16">
                                                <div style="color: #626364; padding-top: 10px">
                                                    {{_current_issue.description}}
                                                </div>
                                            </el-col>
                                            <el-col :xs="3" :md="3">
                                                <div style="color: #626364; padding-top: 10px">
                                                    <div v-bind:class="{'tag' : _current_issue.status != 'solved', 'tag_solved' : _current_issue.status == 'solved'}" style="border-radius: 50px; width: 30px; height: 30px; padding-top: 6px; text-align: center;font-size:13px">{{_current_issue.label}}</div>
                                                </div>
                                            </el-col>
                                        </el-row>
                                        <el-row style="margin-bottom: 0px">
                                            <el-col :xs="12" :xl="12">
                                                <div v-if="_current_issue.comments" style="margin-left: 15px; margin-top: 5px">
                                                    <b>{{_current_issue.comments.length}}</b> comments
                                                </div>
                                            </el-col>
                                        </el-row>
                                    </div>
                                    <el-row type="flex" style="margin-bottom: 0px; margin-top: 0px">
                                        <el-col v-if="_current_issue.project_files_id" :xs="8" :xl="8">
                                            <el-button @click="showIssueImage(_current_issue.project_files.path, _current_issue.project_files.thumb_path, _current_issue.project_files.id)"
                                                type="text" class="issue-button-details" icon="el-icon-picture-outline" style="float: left;margin-left: 15px;"
                                                size="mini">
                                                <span style="font-size: 14px; ">Image attached</span>
                                            </el-button>
                                        </el-col>
                                        <el-col v-else :xs="8" :xl="8">
                                            <el-button v-if="current_rol == 'client' && _current_issue.status != 'solved' && _current_issue.user_id == user.id" @click="editIssue(_current_issue.id)"
                                                type="text" class="issue-button-details" style="float: left;margin-left: 15px"
                                                size="mini">
                                                <span class="fa fa-paperclip" style="font-size: 20px;"></span>
                                                <span style="font-size: 14px;">Add image</span>
                                            </el-button>
                                        </el-col>
                                        <el-col :xs="3" :xl="3" :offset="7">
                                            <el-button v-if="current_rol == 'client' && _current_issue.user_id == user.id && _current_issue.status != 'solved'" @click="setIssueSolved(_current_issue.id, 'solved')"
                                                class="issue-button-solved" icon="el-icon-check" size="mini">
                                            </el-button>
                                        </el-col>
                                        <el-col v-if="" :xs="3" :xl="3">
                                            <el-button v-if="current_rol == 'client' && _current_issue.status != 'solved' && _current_issue.user_id == user.id" @click="editIssue(_current_issue.id)"
                                                class="issue-button-edit" icon="el-icon-edit" size="mini">
                                            </el-button>
                                        </el-col>
                                        <el-col :xs="3" :xl="3">
                                            <el-button @click="IssueDetails(_current_issue.group)" class="issue-button-details" icon="el-icon-search" size="mini">
                                            </el-button>
                                        </el-col>
                                    </el-row>
                                </div>
                            </el-col>
                        </el-row>
                    </div>
                    <!--  </transition-group> -->
                    <!--   <transition-group name="el-fade-in"> -->
                    <div :key="2" v-show="showIssueDetails">
                        <el-row style="padding: 5px; margin-left: 15px">
                            <el-col>
                                <div @click="issuesList()" style="float: left">
                                    <i class="el-icon-back icon-center" style="font-size: 20px"></i>
                                </div>
                            </el-col>
                        </el-row>
                        <el-row type="flex" justify="end" style="margin-bottom: 0px">
                            <el-col>
                                <div style="margin-top: 9px" :class="{'current_issue_active' : issue_datils.active_issue, 'current_issue_normal' : !issue_datils.active_issue, 'current_issue_solved' : issue_datils.status == 'solved'}">
                                    <div v-if="issue_datils.status == 'solved'" style="color: rgb(10, 92, 84) !important; text-align: center; font-weight: bold; padding: 5px">This issue has been resolved</div>
                                    <div v-else style="color: #da4545;; text-align: center; font-weight: bold; padding: 5px">This issue is pending</div>
                                    <el-row style="padding-top: 3%; margin-bottom: 0px">
                                        <el-col :md="4">
                                            <div style="padding: 10px">
                                                <img v-if="issue_datils.user" :src="issue_datils.user.photo_url" class="img-circle special-img" style="width: 50px;height: 50px;padding: 2px;border-style: solid;border-color: #a5a3a3; border-width: 1px;">
                                            </div>
                                        </el-col>
                                        <el-col :md="16">
                                            <div style="color: #626364; padding-top: 10px">
                                                {{issue_datils.description}}
                                            </div>
                                        </el-col>
                                        <el-col :md="4">
                                            <div style="color: #626364; padding-top: 10px">
                                                <div v-bind:class="{'tag' : issue_datils.status != 'solved', 'tag_solved' : issue_datils.status == 'solved'}" style="border-radius: 50px; width: 30px; height: 30px; padding-top: 6px; text-align: center;font-size:13px">{{issue_datils.label}}</div>
                                            </div>
                                        </el-col>
                                    </el-row>
                                    <el-row type="flex" style="margin-bottom: 0px; margin-top: 15px">
                                        <el-col v-if="issue_datils.project_files_id" :xs="8" :xl="8">
                                            <el-button @click="showIssueImage(issue_datils.project_files.path, issue_datils.project_files.thumb_path, issue_datils.project_files.id)"
                                                type="text" class="issue-button-details" icon="el-icon-picture-outline" style="float: left;margin-left: 15px"
                                                size="mini">
                                                <span style="font-size: 14px;">Image attached</span>
                                            </el-button>
                                        </el-col>
                                        <el-col v-else :xs="8" :xl="8">
                                            <el-button v-if="current_rol == 'client' && issue_datils.status != 'solved'" @click="editIssue(issue_datils.id)" type="text"
                                                class="issue-button-details" style="float: left;margin-left: 15px" size="mini">
                                                <span class="fa fa-paperclip" style="font-size: 20px;"></span>
                                                <span style="font-size: 14px">Add image</span>
                                            </el-button>
                                        </el-col>
                                        <el-col :xs="3" :xl="3" :offset="10">
                                            <el-button v-if="current_rol == 'client' && issue_datils.status != 'solved'" @click="editIssue(issue_datils.id)" class="issue-button-details"
                                                icon="el-icon-edit" size="mini">
                                            </el-button>
                                        </el-col>
                                        <el-col :xs="3" :xl="3">
                                            <el-button v-if="current_rol == 'client' && issue_datils.status != 'solved'" @click="deleteIssue()" class="issue-button-edit"
                                                icon="el-icon-delete" size="mini">
                                            </el-button>
                                        </el-col>
                                    </el-row>
                                </div>
                            </el-col>
                        </el-row>
                        <el-row v-if="issue_datils.status != 'solved'" style="padding: 5px; margin-bottom: 0px">
                            <el-input v-model="current_comment.description" type="textarea" :autosize="{ minRows: 4, maxRows: 8}" placeholder="Type comment description..."
                                v-bind:class="{'error_textarea' : errors.comment_description}">
                            </el-input>
                            <span v-if="errors.comment_description" style="float: right; color: rgb(211, 12, 12); font-size: 13px">{{errors_msg.comment_description}}</span>
                        </el-row>
                        <el-row v-if="issue_datils.status != 'solved'" type="flex" justify="end" style="padding-bottom: 0px; margin-bottom: 0px">
                            <!--  <el-col :sm="4">
                                                        <el-button style="border: 0px">
                                                        <i class="fa fa-paperclip fa-2x" style="color: #8f9091; font-size: 22px"></i>
                                                        </el-button>
                                                    </el-col> -->
                            <el-col :sm="4">
                                <el-button @click="addComment(issue_datils.id)" style="border: 0px">
                                    <i class="fa fa-share" style="color: #8f9091; font-size: 22px"></i>
                                </el-button>
                            </el-col>
                        </el-row>
                        <el-row style="padding: 5px; margin-bottom: 0px; height: 400px; overflow-y: scroll; overflow-x: hidden">
                            <div v-for="(comment, key, index ) in issue_datils.comments" :key=key>
                                <div class="comment" style="margin-left: 0px">
                                    <el-row style="margin-bottom: 0px">
                                        <el-col :md="20">
                                            <div>
                                                {{comment.description}}
                                            </div>
                                        </el-col>
                                        <el-col :md="4">
                                            <div style="padding: 10px">
                                                <img v-if="comment.user.photo_url" :src="comment.user.photo_url" class="img-circle special-img" style="width: 45px; height: 45px; padding: 2px; border-style: solid; border-color: rgb(165, 163, 163); border-width: 1px;">
                                                <img v-else :src="comment.user.photo_url" class="img-circle special-img" style="width: 45px; height: 45px; padding: 2px; border-style: solid; border-color: rgb(165, 163, 163); border-width: 1px;">
                                            </div>
                                        </el-col>
                                    </el-row>
                                    <el-row type="flex" style="margin-bottom: 0px; margin-left: 0px">
                                        <el-col v-if="comment.project_files_id" :xs="8" :xl="8">
                                            <el-button @click="showIssueImage(comment.project_files.path, comment.project_files.thumb_path, comment.project_files.id)"
                                                type="text" class="issue-button-details" icon="el-icon-picture-outline" style="float: left"
                                                size="mini">
                                                <span style="font-size: 14px;">Image attached</span>
                                            </el-button>
                                        </el-col>
                                        <el-col v-else :xs="8" :xl="8">
                                            <el-button v-if="issue_datils.status != 'solved' && comment.user_id == user.id" @click="editComment(comment.issue_id, comment.id)"
                                                type="text" class="issue-button-details" style="float: left" size="mini">
                                                <span class="fa fa-paperclip" style="font-size: 20px;"></span>
                                                <span style="font-size: 14px;">Add image</span>
                                            </el-button>
                                        </el-col>
                                        <el-col :xs="3" :xl="3" :offset="10">
                                            <el-button v-if="issue_datils.status != 'solved' && comment.user_id == user.id" @click="editComment(comment.issue_id, comment.id)"
                                                class="issue-button-details" icon="el-icon-edit" size="mini">
                                            </el-button>
                                        </el-col>
                                        <el-col :xs="3" :xl="3">
                                            <el-button v-if="issue_datils.status != 'solved' && comment.user_id == user.id" @click="deleteComment(issue_datils.id, comment.id)"
                                                class="issue-button-edit" icon="el-icon-delete" size="mini">
                                            </el-button>
                                        </el-col>
                                    </el-row>
                                    <!--    <el-row v-if="comment.project_files_id" type="flex" style="margin-bottom: 0px; margin-left: 10px">
                                                            <el-col :xs="24" :xl="24">
                                                            <el-button @click="showIssueImage(comment.project_files.path, comment.project_files.thumb_path, comment.project_files.id)"
                                                                type="text" class="issue-button-details" icon="el-icon-picture-outline" style="float: left" size="mini">
                                                                <span style="font-size: 14px;">Image attached</span>
                                                            </el-button>
                                                            </el-col>
                                                        </el-row> -->
                                </div>
                            </div>
                        </el-row>
                    </div>
                    <!--  </transition-group> -->
                    <!--  </div> -->
                    <!--  </el-col> -->
                </el-row>
            </div>
            <div id="main" style="margin-right: 20%; margin-left: 15%">
                <div id="stage" style="background-color: rgb(224, 223, 223); padding-top: 60px; width: 100%"></div>
            </div>

        </div>
        <el-dialog title="New issue" :top="'5px'" :close-on-click-modal="false" :close-on-press-escape="false" :show-close="false"
            :visible="showIssueDialog" :open="jqueryCssAdjust()" @open="onOpenDialogNewIssue" @close="onCloseDialogNewIssue"
            width="30%" center>
            <el-input type="textarea" ref="autofocus_issue_create" :autofocus="true" :rows="5" placeholder="Type issue description..."
                v-model="new_issue.description" v-bind:class="{'error_textarea' : errors.issue_description}">
            </el-input>
            <span v-if="errors.issue_description" style="float: right; color: rgb(211, 12, 12); font-size: 13px">{{errors_msg.issue_description}}</span>
            <span slot="footer" class="dialog-footer">
                <el-button @click="cancelIssue()">Cancel</el-button>
                <el-button type="primary" @click="addIssue()">Save</el-button>
            </span>
        </el-dialog>

        <el-dialog title="Edit issue" :close-on-click-modal="false" :close-on-press-escape="false" :show-close="false" :visible.sync="showEditableIssueDialog"
            @open="onOpenDialogEditIssue" @close="onCloseDialogEditIssue" width="30%" center>
            <el-input type="textarea" ref="autofocus_issue_edit" :autofocus="true" :rows="5" v-model="editable_issue.description" v-bind:class="{'error_textarea' : errors.editable_issue_description}">
            </el-input>
            <span v-if="errors.editable_issue_description" style="float: right; color: rgb(211, 12, 12); font-size: 13px">{{errors_msg.editable_issue_description}}</span>
            </el-input>
            <span slot="footer" class="dialog-footer">
                <el-button @click="cancelEditableIssue()">Cancel</el-button>
                <el-button type="primary" @click="updateIssue()">Save</el-button>
            </span>
            <el-row v-if="!editable_issue.project_files_id">
                <el-col style="text-align: center">
                    <div class="uploader" style="width: 100%; margin-top: 0px">
                        <el-upload ref="upload" drag :data="imageFormData" action="/api/files/upload" :on-preview="handlePreview" :on-error="handleError"
                            :on-remove="deleteFile" :on-success="handleSuccess" :auto-upload="true" :file-list="fileList" name="photos"
                            list-type="picture">
                            <i class="el-icon-upload"></i>
                            <div class="el-upload__text">Drop file here or
                                <em>click to upload</em>
                            </div>
                        </el-upload>
                    </div>
                </el-col>
            </el-row>
        </el-dialog>

        <el-dialog title="Edit comment" :close-on-click-modal="false" :close-on-press-escape="false" :show-close="false" :visible.sync="showEditableCommentDialog"
            @open="onOpenDialogEditComment" @close="onCloseDialogEditComment" width="30%" center>
            <el-input type="textarea" ref="autofocus_comment" :autofocus="true" :rows="5" v-model="editable_comment.description" v-bind:class="{'error_textarea' : errors.editable_comment_description}">
            </el-input>
            <span v-if="errors.editable_comment_description" style="float: right; color: rgb(211, 12, 12); font-size: 13px">{{errors_msg.editable_comment_description}}</span>
            </el-input>
            <span slot="footer" class="dialog-footer">
                <el-button @click="cancelEditableComment()">Cancel</el-button>
                <el-button type="primary" @click="updateComment()">Save</el-button>
            </span>
            <el-row v-if="!editable_comment.project_files_id">
                <el-col style="text-align: center">
                    <div class="uploader" style="width: 100%; margin-top: 15px">
                        <el-upload ref="upload" drag :data="imageFormData" action="/api/files/upload" :on-preview="handlePreview" :on-error="handleError"
                            :on-remove="deleteFile" :on-success="handleSuccess" :auto-upload="true" :file-list="fileList" name="photos"
                            list-type="picture">
                            <i class="el-icon-upload"></i>
                            <div class="el-upload__text">Drop file here or
                                <em>click to upload</em>
                            </div>
                        </el-upload>
                    </div>
                </el-col>
            </el-row>
        </el-dialog>

        <el-dialog :close-on-click-modal="true" :close-on-press-escape="true" :show-close="true" :visible.sync="showIssueImageFlag"
            width="60%" center>
            <el-row>
                <el-col :xs="4">
                    <div style="padding: 15px">
                        <span v-if="current_issue.status != 'solved'" @click="deletePicture(image_id)" class="fa fa-trash" style="font-size: 24px; cursor: pointer"></span>
                    </div>
                </el-col>
                <el-col :xs="20">
                    <img :src="image_preview" style="width: 100%; height: auto">
                </el-col>
            </el-row>
        </el-dialog>
        <el-dialog :close-on-click-modal="true" :close-on-press-escape="true" :show-close="true" :visible.sync="showSubmitOptions"
            width="40%" center>
            <el-row style="text-align: center">
                <el-col :lg="24">
                    <span style="font-size: 28px">{{user.name}}, please make your decition below.</span>
                </el-col>
            </el-row>
            <el-row style="text-align: center">
                <el-col :lg="24">
                    <span style="font-size: 18px">Are these your corrections
                        <span style="color: #24a158">for revision # {{current_version_number}} ?</span>
                    </span>
                </el-col>
            </el-row>
            <el-row style="text-align: center">
                <el-col :lg="12">
                    <el-button style="background-color: #fa5555; color: #fff; font-weight: bold; border: 0px" @click="sendRevision()" type="primary">Yes, submit corrections
                    </el-button>
                </el-col>
                <el-col :lg="12">
                    <el-button style="background-color: rgb(224, 223, 223); color: rgb(87, 86, 86); font-weight: bold; border: 0px" @click="cancelSubmitCorrections()"
                        type="primary">Cancel
                    </el-button>
                </el-col>
            </el-row>
        </el-dialog>

        <el-dialog :close-on-click-modal="true" :close-on-press-escape="true" :show-close="true" :visible.sync="showApproveDesign"
            width="40%" center>
            <el-row style="text-align: center">
                <el-col :lg="24">
                    <span style="font-size: 28px">{{user.name}}, please make your decition below.</span>
                </el-col>
            </el-row>
            <el-row style="text-align: center">
                <el-col :lg="24">
                    <span style="font-size: 18px">Are you sure you want to approve this design?
                        <span style="color: #24a158">for revision # {{current_version_number}}?</span>
                    </span>
                </el-col>
            </el-row>
            <el-row style="text-align: center">
                <el-col :lg="12">
                    <el-button style="background-color: #fa5555; color: #fff; font-weight: bold; border: 0px" @click="submitDecision" type="primary">Yes, approve design
                    </el-button>
                </el-col>
                <el-col :lg="12">
                    <el-button style="background-color: rgb(224, 223, 223); color: rgb(87, 86, 86); font-weight: bold; border: 0px" @click="cancelApproveCorrections()"
                        type="primary">Cancel
                    </el-button>
                </el-col>
            </el-row>
        </el-dialog>
    </div>

</template>

<script>
    /*   import 'element-ui/lib/theme-chalk/index.css'; */
    import ElementUI from 'element-ui'
    import Konva from "konva";
    import Auth from '../../services/auth'
    import { mapGetters } from "vuex";
    import ElIcon from "../../../../../node_modules/element-ui/packages/icon/src/icon.vue";
    export default {
        components: {ElIcon},
        props: ["project_id", "revision_id", "project_hash"],
        mixins: [Konva],
        data() {
            return {
                stage: {},
                background: {},
                rect: {},
                background_image: {},
                image: {},
                group: {},
                background_group: {},
                text: {},
                selectRect: {},
                draggedDistance: {},
                group_counter: 1,
                current_group_id: "",
                current_group_label: "",
                user: Spark.state.user,
                versions: [],
                current_version_number: "",
                fullscreenLoading: false,
                value: "",
                current_review_id: "",

                proofs: [],
                collaborators: [],
                current_proof: {},
                current_issue: {
                    id: "",
                    group_id: "",
                    proof_id: "",
                    description: "",
                    label: "",
                    user: "",
                    status: "",
                    active_issue: "",
                    comments: []
                },
                new_issue: {
                    id: "",
                    group_id: "",
                    proof_id: "",
                    description: "",
                    label: "",
                    user: "",
                    status: "",
                    active_issue: "",
                    comments: []
                },
                editable_issue: {
                    id: "",
                    group_id: "",
                    proof_id: "",
                    description: "",
                    label: "",
                    user: "",
                    status: '',
                    active_issue: "",
                    comments: [],
                    project_files_id: "",
                    project_files: {}
                },
                current_comment: {
                    id: "",
                    issue_id: "",
                    description: "",
                    label: "",
                    user: ""
                },
                editable_comment: {
                    id: "",
                    issue_id: "",
                    description: "",
                    label: "",
                    user: ""
                },

                issue_datils: {},

                pen_active: false,
                eraser_active: false,
                zoom_in_active: false,
                zoom_out_active: false,
                remove_active: false,
                showIssueDialog: false,
                showIssueDetails: false,
                initialStageWidth: "",
                initialStageHeight: "",
                showEditableIssueDialog: false,
                showEditableCommentDialog: false,
                showSubmitOptions: false,
                showApproveDesign: false,
                posStart: 0,
                posNow: 0,
                headers: {},
                imageFormData: {
                    project_id: "",
                    file_type: "",
                    owner_type: ""
                },
                fileList: [],
                image_preview: "",
                image_thumb: "",
                image_id: "",
                showIssueImageFlag: false,
                disabled_remove: false,
                decision: 'approved',
                current_rol: '',
                showSubmitButtons: false,
                errors: {
                    comment_description: false,
                    issue_description: false,
                    editable_issue_description: false,
                    editable_comment_description: false
                },
                errors_msg: {
                    comment_description: false,
                    issue_description: false,
                    editable_issue_description: false,
                    editable_comment_description: false,
                },
                showSendBackButton: true,
                zoom_level: 1,
                showSideBar: true,
                autofocus_textarea: true
            };
        },
        methods: {
            initializeStage(img_url, image) {
                var self = this;
                self.group_counter = 1;
                this.showIssueDetails = false;

                if (self.current_proof.canvas_data) {
                    self.stage = Konva.Node.create(self.current_proof.canvas_data, "stage");
                    self.background = self.stage.getChildren()[0];
                    self.background.clearBeforeDraw(true);

                    self.background_group = self.background.getChildren()[0];
                    self.background_group.scale({
                        x: 1,
                        y: 1
                    });

                    self.background_group.draggable(true);
                    this.resetPosition();

                    var imageObject = new Image();

                    imageObject.onload = function () {
                        self.background_image = new Konva.Image({
                            x: 0,
                            y: 0,
                            image: imageObject,
                            width: self.initialStageWidth,
                            height: self.initialStageWidth / (this.width / this.height),
                            draggable: false
                        });

                        self.background_group.on("click tap", function (e) {
                            if (e.target.getClassName() !== 'Circle' && e.target.getClassName() !== 'Text') {
                                if (self.current_rol == 'client') {
                                    if (e.evt.button === 2) {
                                        return false;
                                    }
                                    self.drawCircle();
                                }
                            } else {
                                return false;
                            }

                        });

                        self.background_group.on("dragmove", function (e) {
                            self.stage.container().style.cursor = 'move';
                        });
                        self.background_group.on("dragend", function (e) {
                            self.stage.container().style.cursor = 'default';
                        });

                        self.background_group.add(self.background_image);
                        self.background_image.moveToBottom();
                        self.background.draw();
                    }

                    imageObject.src = img_url;

                    self.groups = self.background_group.getChildren(function (node) {
                        if (node.getClassName() === "Group") {
                            node.on("dragend", function () {
                                self.saveCanvasState();
                            });

                            node.getChildren(function (node_1) {
                                if (node_1.getClassName() === "Circle") {
                                    node_1.radius(20 / self.background_group.scaleX());
                                    node_1.strokeWidth(2 / self.background_group.scaleX());
                                }
                                if (node_1.getClassName() === "Text") {
                                    node_1.fontSize(15 / self.background_group.scaleX());
                                    node_1.offsetX(8 / self.background_group.scaleX());
                                    node_1.offsetY(8 / self.background_group.scaleY());
                                }
                            });

                            node.on("click tap", function (event) {
                                /*  var scale = new Konva.Animation(function (frame) {
                                     node.scale({
                                         x: node.scaleX() + 0.01,
                                         y: node.scaleY() + 0.01,
                                     })
                                 }, self.background);
 
                                 var normal = new Konva.Animation(function (frame) {
                                     node.scale({
                                         x: 1,
                                         y: 1,
                                     })
                                 }, self.background);
 
                                 setTimeout(function(){ scale.start(); }, 100);
                                 setTimeout(function(){ normal.start(); }, 1000); */



                                self.current_group_id = node.getAttr("id");
                                self.current_group_label = node.getAttr("label");
                                var circle = self.current_proof.issues.forEach(function (item) {
                                    if (item.group == event.target.getAttr("group_id")) {
                                        self.current_issue = item;
                                    }
                                });

                                self.IssueDetails(self.current_group_id);
                                self.activate_remove();
                            });
                        }
                        return node.getClassName() === "Group";
                    });

                    self.groups.forEach(function (group, index) {
                        if (group.getAttr("counter") >= self.group_counter) {
                            self.group_counter = group.getAttr("counter");
                            self.group_counter++;
                        }
                    });

                    self.texts = self.background.getChildren(function (node) {
                        return node.getClassName() == "Text";
                    });

                } else {
                    self.stage = new Konva.Stage({
                        container: "stage",
                        width: self.initialStageWidth,
                        height: self.initialStageWidth,
                        draggable: false
                    });

                    self.background = new Konva.Layer({

                    });

                    self.background_group = new Konva.Group({
                        id: 'background_group',
                        name: 'background_group',
                        clearBeforeDraw: true,
                        draggable: true,
                        x: 0,
                        y: 0
                    });

                    var imageObject = new Image();
                    imageObject.onload = function () {
                        self.background_image = new Konva.Image({
                            x: 0,
                            y: 0,
                            width: self.initialStageWidth,
                            height: self.initialStageWidth / (this.width / this.height),
                            image: imageObject
                        });


                        self.background_group.on("click tap", function (e) {
                            if (e.target.getClassName() !== 'Circle' && e.target.getClassName() !== 'Text') {
                                if (self.current_rol == 'client') {
                                    if (e.evt.button === 2) {
                                        return false;
                                    }
                                    self.drawCircle();
                                }
                            } else {
                                return false;
                            }
                        });

                        self.groups = self.background_group.getChildren(function (node) {
                            if (node.getClassName() === "Group") {
                                node.on("dragend", function () {
                                    self.saveCanvasState();
                                });
                                node.on("click tap", function (event) {
                                    var children = self.groups.getChildren(function (node) {
                                        /*   if (node.getClassName() === "Circle" || node.getClassName() === "Text") {
                                            if (node.getAttr('id') == event.target.getAttr('id')) {
                                              node.fill('#0a7738');
                                              node.stroke('#26d35131');
                                              self.stage.draw();
                                            } else {
                                              node.fill('#fff');
                                              node.stroke('#26d35131');
                                              self.stage.draw();
                                            }
                        
                                          } */
                                    });
                                    self.current_group_id = node.getAttr("id");
                                    self.current_group_label = node.getAttr("label");
                                    var circle = self.current_proof.issues.forEach(function (item) {
                                        if (item.group == event.target.getAttr("group_id")) {
                                            self.current_issue = item;
                                        }
                                    });

                                    self.IssueDetails(self.current_group_id);
                                    self.activate_remove();
                                });
                            }
                            return node.getClassName() === "Group";
                        });

                        self.background_group.add(self.background_image);
                        self.background.add(self.background_group);
                        self.background_image.moveToTop();
                        self.stage.add(self.background);
                        self.saveCanvasState();
                    }

                    imageObject.src = img_url;

                    // draw a rectangle to be used as the rubber area
                    /*  self.selectRect = new Konva.Rect({ x: 0, y: 0, width: 0, height: 0, stroke: '#f07f7f', strokeWidth: 4 })
                       self.selectRect.listening(false); // stop r2 catching our mouse events.
                       self.background.add(self.selectRect);
                       self.stage.draw(); */

                    /* var mode = ''; */

                    /*    self.rect.on("click tap", function () {
                         self.drawCircle();
                       }); */



                    /* self.rect.on('mousedown', function (e) {
                        mode = 'drawing';
                        self.startDrag({ x: e.evt.layerX, y: e.evt.layerY })
                      }) */

                    // update the rubber rect on mouse move - note use of 'mode' var to avoid drawing after mouse released.
                    /*  self.rect.on('mousemove', function (e) {
                         if (mode === 'drawing') {
                           self.updateDrag({ x: e.evt.layerX, y: e.evt.layerY })
                         }
                       }) */

                    /*  self.rect.on('mouseup', function (e) {
                         mode = '';
                         self.selectRect.visible(true);
                         self.stage.draw();
                       }) */
                }

                /* this.fitStageIntoParentContainer();
                window.addEventListener("resize", this.fitStageIntoParentContainer); */
            },
            /*  startDrag(posIn) {
                 this.posStart = { x: posIn.x, y: posIn.y };
                 this.posNow = { x: posIn.x, y: posIn.y };
               }, */
            /* updateDrag(posIn) {
                // update rubber rect position
                this.posNow = { x: posIn.x, y: posIn.y };
                var posRect = this.reverse(this.posStart, this.posNow);
                this.selectRect.x(posRect.x1);
                this.selectRect.y(posRect.y1);
                this.selectRect.width(posRect.x2 - posRect.x1);
                this.selectRect.height(posRect.y2 - posRect.y1);
                this.selectRect.visible(true);
                this.stage.draw(); // redraw any changes.
        
              }, */
            /*  reverse(r1, r2) {
                 var r1x = r1.x, r1y = r1.y, r2x = r2.x, r2y = r2.y, d;
                 if (r1x > r2x) {
                   d = Math.abs(r1x - r2x);
                   r1x = r2x; r2x = r1x + d;
                 }
                 if (r1y > r2y) {
                   d = Math.abs(r1y - r2y);
                   r1y = r2y; r2y = r1y + d;
                 }
                 return ({ x1: r1x, y1: r1y, x2: r2x, y2: r2y }); // return the corrected rect.     
               }, */
            /*  fitStageIntoParentContainer() {
               var container = document.querySelector("#stage-parent");
       
               // now we need to fit stage into parent
               var containerWidth = container.offsetWidth;
               // to do this we need to scale the stage
               var scale = containerWidth / this.initialStageWidth;
       
               this.stage.width(this.initialStageWidth * scale);
               this.stage.height(this.initialStageHeight * scale);
               this.stage.scale({ x: scale, y: scale });
               this.stage.draw();
             }, */
            reset_group(node) {
                if (node.getClassName() === "Circle") {
                    node.fill('#f07f7f');
                    node.stroke('#f76565')
                    this.stage.draw();
                }
            },
            resetZomm() {
                this.zoom_level = 1;
            },
            resetPosition() {
                this.background_group.x(0);
                this.background_group.y(0);
                this.resetZomm();
            },
            drawCircle() {
                var self = this;
                if (this.pen_active) {
                    var group = new Konva.Group({
                        id: "group_" + self.group_counter,
                        label: self.group_counter,
                        counter: self.group_counter,
                        x: (self.stage.getPointerPosition().x - self.background_group.getAttr('x')) / self.background_group.scaleX(),
                        y: (self.stage.getPointerPosition().y - self.background_group.getAttr('y')) / self.background_group.scaleY(),
                        draggable: true
                    });

                    var circle = new Konva.Circle({
                        id: "circle_" + group.getAttr("id"),
                        group_id: group.getAttr("id"),
                        counter: group.getAttr("counter"),
                        radius: 20 / self.background_group.scaleX(),
                        fill: "#f07f7f",
                        stroke: "#f76565",
                        strokeWidth: 2 / self.background_group.scaleX(),
                    });
                    /* 
                                        circle.scaleX(1);
                                        circle.scaleY(1); */

                    var text = new Konva.Text({
                        id: "text_" + group.getAttr("id"),
                        group_id: group.getAttr("id"),
                        counter: group.getAttr("counter"),
                        text: self.group_counter,
                        fontSize: 15 / self.background_group.scaleX(),
                        fontFamily: "Arial",
                        fill: "white",
                        offsetX: 8 / self.background_group.scaleX(),
                        offsetY: 8 / self.background_group.scaleY(),
                    });

                    this.current_group_id = group.getAttr("id");
                    this.current_group_label = group.getAttr("label");
                    /* this.resetZomm(); */
                    group.add(circle);
                    group.add(text);

                    this.group_counter++;

                    group.on("dragend", function () {
                        self.saveCanvasState();
                    });

                    group.on("click tap", function (event) {
                        self.current_group_id = event.target.getAttr("group_id");
                        self.current_group_label = group.getAttr("label");

                        self.current_proof.issues.forEach(function (item) {
                            if (item.group == event.target.getAttr("group_id")) {
                                self.current_issue = item;
                            }
                        });

                        self.IssueDetails(self.current_group_id);
                        /* self.activate_remove(); */
                    });

                    this.background_group.add(group);
                    this.background.add(this.background_group);
                    this.stage.add(this.background);
                    this.showIssueDialog = true;
                }
            },

            loadThumb(index) {
                var self = this;
                this.showSubmitButtons = true;
                self.current_proof = self.proofs[index];
                if (self.current_proof.issues) {
                    if (self.current_proof.issues.length > 0) {
                        self.current_proof.issues.sort(function (a, b) {
                            return b.label - a.label;
                        });
                    }
                }
                var img_url = self.current_proof.picture_url;
                self.current_proof.active_thumb = true;
                for (var i in self.proofs) {
                    if (i == index) {
                        self.proofs[i].active_thumb = true;
                    } else {
                        self.proofs[i].active_thumb = false;
                    }
                }
                self.initializeStage(img_url);
            },
            saveCanvasState() {
                var self = this;
                if (self.current_proof) {
                    var formData = {
                        revision_id: self.current_proof.revision_id,
                        id: self.current_proof.id,
                        canvas_data: self.stage.toJSON()
                    };
                    axios
                        .post("/api/proof/save", formData)
                        .then(response => {
                            if (response.data.status == 1) {
                                for (var i in self.proofs) {
                                    if (self.proofs[i].id == self.current_proof.id) {
                                        var proof = self.proofs[i];
                                        proof.canvas_data = self.stage.toJSON();
                                        self.proofs.splice(
                                            self.proofs.indexOf(self.current_proof),
                                            1,
                                            proof
                                        );
                                    }
                                }

                                self.fullscreenLoading = false;
                                self.$store.commit("SAVE_CURRENT_PROOF_DATA", self.proofs);
                            } else {
                                self.handle_error(response.data.errors);
                            }
                        })
                        .catch(error => {
                            self.handle_error({});
                        });
                }
            },

            deleteIssue() {
                var self = this;
                self.showIssueDialog = false;
                swal({
                    title: "Are you sure?",
                    text: "You won't be able to revert this!",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#3085d6",
                    cancelButtonColor: "#d33",
                    confirmButtonText: "Yes, delete it!"
                }).then(result => {
                    if (result.value) {
                        axios
                            .delete("/api/proof/delete_issue/" + self.current_issue.id)
                            .then(response => {
                                if (response.data.status == 1) {
                                    self.current_proof.issues.forEach(function (item, index) {
                                        if (item.id == self.current_issue.id) {
                                            var removed = self.current_proof.issues.splice(index, 1);
                                            if (removed) {
                                                var group = self.background.find(
                                                    "#" + self.current_group_id
                                                );
                                                group.remove();
                                                self.background.draw();
                                                self.saveCanvasState();
                                                self.showIssueDialog = false;
                                                self.activate_pen();
                                                self.issuesList();
                                                self.resetIssue();
                                            }
                                        }
                                    });
                                } else {
                                    this.handle_error(response.data.errors);
                                }
                            })
                            .catch(error => {
                                this.handle_error({});
                            });
                    }
                });
            },
            deleteIssueById(issue_id) { },
            editIssue(issue_id) {
                var self = this;
                this.current_proof.issues.forEach(function (item, index) {
                    if (item.id == issue_id) {
                        self.editable_issue.id = item.id;
                        self.editable_issue.description = item.description;
                        self.editable_issue.project_files_id = item.project_files_id;
                        self.showEditableIssueDialog = true;
                    }
                });
            },
            editComment(issue_id, comment_id) {
                var self = this;
                this.current_proof.issues.forEach(function (item, index_i) {
                    if (item.id == issue_id) {
                        item.comments.forEach(function (comment, index_j) {
                            if (comment.id == comment_id) {
                                self.editable_comment.id = comment.id;
                                self.editable_comment.issue_id = comment.issue_id;
                                self.editable_comment.description = comment.description;
                                self.showEditableCommentDialog = true;
                            }
                        });
                    }
                });
            },
            addIssue() {
                this.reset_errors();
                if (this.new_issue.description != '') {
                    var self = this;
                    var formData = {
                        proof_id: this.current_proof.id,
                        description: this.new_issue.description,
                        group: this.current_group_id,
                        label: this.current_group_label
                    };
                    if (this.new_issue.id > 0) {
                        formData.id = this.new_issue.id;
                    }

                    this.fullscreenLoading = true;
                    axios
                        .post("/api/proof/create_issue", formData)
                        .then(response => {
                            if (response.data.status == 1) {
                                var issue = {
                                    id: response.data.data.id,
                                    proof_id: response.data.data.proof_id,
                                    comments: response.data.data.comments,
                                    status: response.data.data.status,
                                    user_id: response.data.data.user_id,
                                    user: response.data.data.user,
                                    description: response.data.data.description,
                                    group: this.current_group_id,
                                    label: this.current_group_label,
                                    active_issue: true
                                };
                                this.fullscreenLoading = false;
                                if (this.new_issue.id > 0) {
                                    if (this.current_proof.issues) {
                                        if (this.current_proof.issues.length > 0) {
                                            this.current_proof.issues.forEach(function (item, index) {
                                                if (item.id == self.current_issue.id) {
                                                    self.current_proof.issues.splice(index, 1, issue);
                                                } else {
                                                    this.current_proof.issues.push(issue);
                                                }
                                            });
                                        }
                                    }
                                } else {
                                    if (this.current_proof.issues) {
                                        if (this.current_proof.issues.length > 0) {
                                            this.current_proof.issues.push(issue);
                                            this.current_proof.issues.sort(function (a, b) {
                                                return b.label - a.label;
                                            });
                                        }
                                        else {
                                            this.current_proof.issues.push(issue);
                                        }
                                    } else {
                                        this.current_proof.issues = [];
                                        this.current_proof.issues.push(issue);
                                    }
                                }

                                this.activate_issue(this.new_issue.id);
                                this.saveCanvasState();
                                this.issuesList();
                                this.resetNewIssue();
                                this.showIssueDialog = false;

                                this.$store.commit("SAVE_CURRENT_PROOF_DATA", this.proofs);
                            } else {
                                this.fullscreenLoading = false;
                                this.handle_error(response.data.errors);
                            }
                        })
                        .catch(error => {
                            this.fullscreenLoading = false;
                            this.handle_error({});
                        });
                } else {
                    if (this.new_issue.description == '') {
                        this.errors.issue_description = true;
                        this.errors_msg.issue_description = 'Issue description is required';
                    }
                }
            },
            updateIssue() {
                this.reset_errors();
                if (this.editable_issue.description != '') {
                    var self = this;
                    var formData = {
                        id: this.editable_issue.id,
                        description: this.editable_issue.description
                    };
                    this.fullscreenLoading = true;
                    axios
                        .post("/api/proof/create_issue", formData)
                        .then(response => {
                            if (response.data.status == 1) {
                                var issue = {
                                    id: response.data.data.id,
                                    proof_id: response.data.data.proof_id,
                                    comments: response.data.data.comments,
                                    user: response.data.data.user,
                                    user_id: response.data.data.user_id,
                                    description: response.data.data.description,
                                    project_files_id: response.data.data.project_files_id,
                                    project_files: response.data.data.project_files,
                                    group: response.data.data.group,
                                    label: response.data.data.label,
                                    active_issue: true
                                };
                                self.fullscreenLoading = false;
                                self.current_proof.issues.forEach(function (item, index) {
                                    if (item.id == self.editable_issue.id) {
                                        issue.comments = item.comments;
                                        self.current_proof.issues.splice(index, 1, issue);
                                    }
                                });
                                self.activate_issue(this.current_issue.id);
                                self.issuesList();
                                self.resetEditableIssue();
                                self.showEditableIssueDialog = false;
                            } else {
                                self.fullscreenLoading = false;
                                self.handle_error(response.data.errors);
                            }
                        })
                        .catch(error => {
                            self.fullscreenLoading = false;
                            self.handle_error({});
                        });
                } else {
                    if (this.editable_issue.description == '') {
                        this.errors.editable_issue_description = true;
                        this.errors_msg.editable_issue_description = 'Issue description is required';
                    }
                }

            },

            updateComment() {
                this.reset_errors();
                if (this.editable_comment.description != '') {
                    var self = this;
                    var formData = {
                        id: this.editable_comment.id,
                        issue_id: this.editable_comment.issue_id,
                        description: this.editable_comment.description
                    };
                    this.fullscreenLoading = true;
                    axios
                        .post("/api/proof/add_comment", formData)
                        .then(response => {
                            if (response.data.status == 1) {
                                var comment = {
                                    id: response.data.data.id,
                                    issue_id: response.data.data.issue_id,
                                    project_files_id: response.data.data.project_files_id,
                                    user: response.data.data.user,
                                    user_id: response.data.data.user_id,
                                    description: response.data.data.description,
                                    project_file_id: response.data.data.project_file_id,
                                    project_files: response.data.data.project_files
                                };
                                self.fullscreenLoading = false;
                                self.current_proof.issues.forEach(function (item, index) {
                                    if (item.id == self.editable_comment.issue_id) {
                                        item.comments.forEach(function (_comment, index_j) {
                                            if (_comment.id == self.editable_comment.id) {
                                                item.comments.splice(index_j, 1, comment);
                                            }
                                        });
                                    }
                                });

                                self.resetEditableComment();
                                self.showEditableCommentDialog = false;
                            } else {
                                self.fullscreenLoading = false;
                                self.handle_error(response.data.errors);
                            }
                        })
                        .catch(error => {
                            self.fullscreenLoading = false;
                            self.handle_error({});
                        });
                } else {
                    if (this.editable_comment.description == '') {
                        this.errors.editable_comment_description = true;
                        this.errors_msg.editable_comment_description = 'Comment description is required';
                    }

                }

            },

            addComment(issue_id) {
                this.reset_errors();
                if (this.current_comment.description != '') {
                    var self = this;
                    var formData = {
                        issue_id: issue_id,
                        description: self.current_comment.description
                    };
                    self.fullscreenLoading = true;
                    axios
                        .post("/api/proof/add_comment", formData)
                        .then(response => {
                            if (response.data.status == 1) {
                                var comment = {
                                    id: response.data.data.id,
                                    issue_id: response.data.data.issue_id,
                                    user: response.data.data.user,
                                    user_id: response.data.data.user_id,
                                    description: response.data.data.description
                                };
                                self.fullscreenLoading = false;
                                if (self.current_proof.issues.length > 0) {
                                    self.current_proof.issues.forEach(function (issue_, index) {
                                        if (issue_.id == issue_id) {
                                            if (issue_.comments) {
                                                issue_.comments.push(comment);
                                                issue_.comments.sort(function (a, b) {
                                                    return b.id - a.id;
                                                });
                                            } else {
                                                issue_.comments = [];
                                                issue_.comments.push(comment);
                                            }
                                        }
                                    });
                                }
                                self.resetComment();
                            } else {
                                self.handle_error(response.data.errors);
                            }
                        })
                        .catch(error => {
                            self.handle_error({});
                        });
                } else {
                    this.errors.comment_description = true;
                    this.errors_msg.comment_description = 'Comment description is required';
                }
            },
            loadIssue() {
                for (var i in this.current_proof.issues) {
                    if (this.current_proof.issues[i].id == this.current_group_id) {
                        this.current_issue.description = this.current_proof.issues[
                            i
                        ].description;
                    }
                }
            },
            deleteFile(file, fileList) {
                var self = this;
                this.fullscreenLoading = true;
                axios
                    .delete("/api/files/delete/" + file.response.data.id)
                    .then(response => {
                        if (response.data.status == 1) {
                            self.fullscreenLoading = false;
                        } else {
                            self.handle_error(response.data.errors);
                        }
                    })
                    .catch(error => {
                        self.handle_error({});
                    });
            },
            deletePicture(id) {
                var self = this;
                self.fullscreenLoading = true;
                axios
                    .delete("/api/files/delete/" + id)
                    .then(response => {

                        if (response.data.status == 1) {
                            self.fullscreenLoading = false;
                            self.showIssueImageFlag = false;
                            if (response.data.data == "Issue") {

                                if (self.current_proof.issues.length > 0) {
                                    self.current_proof.issues.forEach(function (item, index) {
                                        if (item.project_files_id == id) {

                                            item.project_files_id = "";
                                        }
                                    });
                                }
                            }
                            if (response.data.data == "Comment") {
                                if (self.current_proof.issues.length > 0) {
                                    for (var i = 0; i < self.current_proof.issues.length; i++) {
                                        for (
                                            var j = 0;
                                            j < self.current_proof.issues[i].comments.length;
                                            j++
                                        ) {
                                            if (
                                                self.current_proof.issues[i].comments[j]
                                                    .project_files_id == id
                                            ) {
                                                self.current_proof.issues[i].comments[
                                                    j
                                                ].project_files_id = "";
                                            }
                                        }
                                    }
                                }
                            }
                        } else {
                            self.fullscreenLoading = false;
                            self.showIssueImageFlag = false;
                            self.handle_error(response.data.errors);
                        }
                    })
                    .catch(error => {
                        self.fullscreenLoading = false;
                        self.showIssueImageFlag = false;
                        self.handle_error({});
                    });
            },
            showIssueImage(path, thumb, id) {
                this.image_preview = "/storage/" + path;
                this.image_thumb = thumb;
                this.image_id = id;
                this.showIssueImageFlag = true;
            },
            showSubmitOptionsModal() {
                this.showSubmitOptions = true;
            },
            showApproveDesignModal() {
                this.showApproveDesign = true;
            },
            activate_pen() {
                this.pen_active = !this.pen_active;
                this.zoom_in_active = false;
                this.zoom_out_active = false;
                this.remove_active = false;
                this.disabled_remove = true;
            },
            deactivate_zoom_in() {
                this.zoom_out_active = false;
            },
            deactivate_zoom_out() {
                this.zoom_in_active = false;
            },
            activate_zoom_in() {
                this.zoom_level = this.zoom_level - 0.1;
                this.zoom_out_active = true;
                /*this.pen_active = false;
                this.zoom_out_active = false;
                this.remove_active = false;
                this.disabled_remove = true; */
            },
            activate_zoom_out() {
                this.zoom_level = this.zoom_level + 0.1;
                this.zoom_in_active = true;
                /*this.pen_active = false;
                this.zoom_in_active = false;
                this.disabled_remove = true;
                this.remove_active = false; */
            },
            activate_remove() {
                this.remove_active = true;
                this.disabled_remove = false;
                this.pen_active = false;
                this.zoom_in_active = false;
                this.zoom_out_active = false;
            },
            disable_buttons() {
                this.remove_active = false;
                this.disabled_remove = true;
                this.pen_active = false;
                this.zoom_in_active = false;
                this.zoom_out_active = false;
            },
            reset_errors() {
                this.errors.comment_description = false;
                this.errors.issue_description = false;
                this.errors.editable_issue_description = false;
                this.errors.editable_comment_description = false;
            },
            goBack() {
                /* this.$router.push({ name: 'projects_list' }); */
                window.location.href = "/projects";
            },
            showInviteCollaboratorModal: async function () {
                const {value: email} = await swal({
                    title: 'Inivte people to project',
                    text: 'The person will gain full access to this proof including screens, assets and activity.',
                    input: 'email',
                    inputPlaceholder: 'Please add another collaborator here by email',
                    confirmButtonText: 'SEND INVITE',
                    confirmButtonColor: '#d63c49'
                });

                var self = this;

                if (email) {
                    let formData = {
                        project_id: self.project_id,
                        email: email
                    };

                    self.fullscreenLoading = true;
                    axios
                        .post("/api/projects/send_invite", formData)
                        .then(response => {
                            self.fullscreenLoading = false
                            if (response.data.status == 1) {
                                swal({type: 'success', title: 'Successfully sent an invitation!'})
                            } else {
                                this.handle_error(response.data.errors);
                            }
                        })
                        .catch(error => {
                            self.handle_error({});
                        });
                }
            },
            submitDecision() {
                var self = this;
                if (this.decision != '') {
                    var formData = {
                        project_id: self.project_id,
                        revision_id: self.current_proof.revision_id,
                        decision: this.decision
                    }
                    self.fullscreenLoading = true;
                    axios
                        .post("/api/projects/submit_decision", formData)
                        .then(response => {
                            self.fullscreenLoading = false
                            if (response.data.status == 1) {
                                self.showApproveDesign = false;
                                for (var i in self.current_proof.issues) {
                                    self.current_proof.issues[i].status = 'solved';
                                }
                                swal(
                                    '',
                                    'The decision has been sent by email successfully',
                                    'success'
                                )
                            } else {
                                this.handle_error(response.data.errors);
                            }
                        })
                        .catch(error => {
                            self.handle_error({});
                        });
                    /* if (_decision == 'approved') {
                        var msg_text = 'You are about to request a new revision. This means all the issues in the current one have been solved and you are ready to receive new revisions.'

                    }
                    if (_decision == 'finished') {
                        var msg_text = 'You are about to set this project as approved. This means all the issues has been resolved and you agree with final results.'
                    }
                    swal({
                        title: "Decision submit note",
                        text: msg_text,
                        type: "warning",
                        showCancelButton: true,
                        confirmButtonColor: "#3085d6",
                        cancelButtonColor: "#d33",
                        confirmButtonText: "Yes, send it!"
                    }).then(result => {
                        if (result.value) {
                            
                        }
                    }); */
                }
            },
            sendRevision() {
                this.fullscreenLoading = true;
                axios
                    .get("/api/projects/sendBackRevision/" + this.project_id + '/' + this.current_rol)
                    .then(response => {
                        if (response.data.status == 1) {
                            this.fullscreenLoading = false;
                            if (response.data.status == 1) {
                                this.showSubmitOptions = false;
                                swal(
                                    '',
                                    'The revision has been sent by email successfully',
                                    'success'
                                );

                            } else {
                                this.handle_error(response.data.errors);
                            }
                        } else {
                            this.handle_error(response.data.errors);
                        }
                    })
                    .catch(error => {
                        this.handle_error({});
                    });
            },
            setIssueSolved(issue_id, status) {
                var msg_text = 'You are about to set this issue as resolved.'
                swal({
                    title: "Set issue as resolved",
                    text: msg_text,
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#3085d6",
                    cancelButtonColor: "#d33",
                    confirmButtonText: "Yes"
                }).then(result => {
                    if (result.value) {
                        self.fullscreenLoading = true;
                        axios
                            .get("/api/proof/change_issue_status/" + issue_id + '/' + status)
                            .then(response => {
                                if (response.data.status == 1) {
                                    self.fullscreenLoading = false;
                                    if (this.current_proof.issues.length > 0) {
                                        this.current_proof.issues.forEach(function (item, index) {
                                            if (item.id == issue_id) {
                                                item.status = status;
                                            }
                                        });
                                    }

                                } else {
                                    self.handle_error(response.data.errors);
                                }
                            })
                            .catch(error => {
                                self.handle_error({});
                            });
                    }
                });
            },
            cancelIssue() {
                var group = this.background.find("#" + this.current_group_id);
                group.remove();
                this.group_counter--;
                this.background.draw();
                /*   this.resetZomm(); */
                /*   this.applyZoom(); */
                /*  this.saveCanvasState(); */
                this.resetNewIssue();
                this.showIssueDialog = false;
            },
            cancelEditableIssue() {
                this.resetEditableIssue();
                this.showEditableIssueDialog = false;
            },
            cancelEditableComment() {
                this.resetEditableComment();
                this.showEditableCommentDialog = false;
            },
            resetIssue() {
                this.current_issue.id = "";
                this.current_issue.description = "";
                this.current_issue.label = "";
            },
            resetNewIssue() {
                this.new_issue.id = "";
                this.new_issue.description = "";
                this.new_issue.label = "";
            },
            resetEditableIssue() {
                this.editable_issue.id = "";
                this.editable_issue.description = "";
            },
            resetEditableComment() {
                this.editable_comment.id = "";
                this.editable_comment.description = "";
            },
            resetComment() {
                this.current_comment.id = "";
                this.current_comment.issue_id = "";
                this.current_comment.description = "";
            },
            activate_group(group_id) {
                var current_group = this.stage.find("#" + group_id);
                /*  current_group.getChildren(function (node) {
                   console.log(node);
                 }); */
                /* var circle = this.stage.find("#" + circle_id);
                console.log(circle);
                circle.fill("#fff");
                this.stage.draw(); */
                // {

                /* if (node.getClassName() == 'Circle') {
                    if (node.getAttr('group_id') == group_id) {
                      node.stroke('#fff');
                    } else {
                      node.stroke('#f76565');
                    }
                  } */
                //});
                /*  circle.stroke('#fff');
                   console.log(circle); */
            },
            onOpenDialogNewIssue() {
                var self = this;
                this.showIssueDetails = false;
                this.imageFormData.owner_type = "issue";
                this.imageFormData.issue_id = this.current_issue.id;
                /* this.$refs.autofocus_issues.focus(); */
                Vue.nextTick(function () {
                    self.$refs.autofocus_issue_create.focus();
                });

            },
            onOpenDialogEditIssue() {
                var self = this;
                this.imageFormData.owner_type = "issue";
                this.imageFormData.issue_id = this.editable_issue.id;
                Vue.nextTick(function () {
                    self.$refs.autofocus_issue_edit.focus();
                });
            },
            onOpenDialogEditComment() {
                var self = this;
                this.imageFormData.owner_type = "comment";
                this.imageFormData.comment_id = this.editable_comment.id;
                Vue.nextTick(function () {
                    self.$refs.autofocus_comment.focus();
                });
            },
            onCloseDialogNewIssue() {
                this.reset_errors();
                this.imageFormData.owner_type = "";
            },
            onCloseDialogEditIssue() {
                this.imageFormData.owner_type = "";
                this.fileList = [];
            },
            onCloseDialogEditComment() {
                this.imageFormData.owner_type = "";
                this.fileList = [];
            },

            activate_issue(issue_id) {
                if (this.current_proof.issues.length > 0) {
                    for (var i in this.current_proof.issues) {
                        if (this.current_proof.issues[i].id == issue_id) {
                            this.current_proof.issues[i].active_issue = true;
                        } else {
                            this.current_proof.issues[i].active_issue = false;
                        }
                    }
                }
            },
            IssueDetails(current_group_id) {
                this.current_group_id = current_group_id;
                if (this.current_proof.issues.length > 0) {
                    for (var i in this.current_proof.issues) {
                        if (this.current_proof.issues[i].group == current_group_id) {
                            this.current_issue.id = this.current_proof.issues[i].id;
                            this.issue_datils = this.current_proof.issues[i];
                            this.issue_datils.active_issue = true;
                            if (this.current_proof.issues[i].comments) {
                                if (this.current_proof.issues[i].comments.length > 0) {
                                    this.current_proof.issues[i].comments.sort(function (a, b) {
                                        return b.id - a.id;
                                    });
                                }
                            } else {
                                this.current_proof.issues[i].comments = [];
                            }
                            this.showIssueDetails = true;
                        } else {
                            this.current_proof.issues[i].active_issue = false;
                        }
                    }
                }
            },
            issuesList() {
                this.errors.comment_description = false;
                this.showIssueDetails = false;
            },
            deleteComment(issue_id, comment_id) {
                var self = this;
                swal({
                    title: "Are you sure?",
                    text: "You won't be able to revert this!",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#3085d6",
                    cancelButtonColor: "#d33",
                    confirmButtonText: "Yes, delete it!"
                }).then(result => {
                    if (result.value) {
                        if (issue_id > 0 && comment_id > 0) {
                            axios
                                .delete("/api/proof/delete_comment/" + comment_id)
                                .then(response => {
                                    if (response.data.status == 1) {
                                        self.fullscreenLoading = false;
                                        self.current_proof.issues.forEach(function (issue_, index) {
                                            if (issue_.id == issue_id) {
                                                issue_.comments.forEach(function (comment, index_j) {
                                                    if (comment.id == comment_id) {
                                                        issue_.comments.splice(index_j, 1);
                                                    }
                                                });
                                            }
                                        });
                                    } else {
                                        self.handle_error(response.data.errors);
                                    }
                                })
                                .catch(error => {
                                    self.handle_error({});
                                });
                        }
                    }
                });
            },

            cancelEditComment() {
                this.showCommentDialog = false;
            },
            loadCollaborators(project_id, next) {
                var self = this;
                self.fullscreenLoading = true;
                axios
                    .get('/api/projects/' + project_id + '/collaborators')
                    .then(response => {
                        if (response.data.status == 1) {
                            self.collaborators = response.data.collaborators;
                        }
                        self.fullscreenLoading = false;

                        next();
                    })
                    .catch(error => {
                        self.sendLoading = false;
                        next();
                    });
            },
            loadInitialRevision(project_id, revision_id) {
                var self = this;
                self.fullscreenLoading = true;
                axios
                    .get("/api/projects/load/" + project_id + '/' + revision_id)
                    .then(response => {
                        if (response.data.status == 1) {
                            self.versions = response.data.data.versions;
                            self.current_version_number = response.data.data.last_revision.version;
                            self.value = 'V' + response.data.data.last_revision.version;
                            self.current_review_id = response.data.data.last_revision.id;
                            if (response.data.data.last_revision.proofs.length > 0) {
                                response.data.data.last_revision.proofs.forEach(function (element, index) {
                                    var proof = {
                                        id: element.id,
                                        revision_id: element.revision_id,
                                        project_id: response.data.data.last_revision.project_id,
                                        active_thumb: false,
                                        picture_url: "/storage/" + element.project_files.path,
                                        thumb_url: "/storage/" + element.project_files.path,
                                        canvas_data: element.canvas_data,
                                        issues: element.issues
                                    };
                                    self.proofs.push(proof);
                                    self.fullscreenLoading = false;
                                });
                                if (self.proofs.length > 0) {
                                    self.loadThumb(0);
                                    self.pen_active = true;
                                }
                            } else {
                                self.fullscreenLoading = false;
                            }
                        } else {
                            this.handle_error(response.data.errors);
                        }
                    })
                    .catch(error => {
                        self.sendLoading = false;
                    });
            },
            loadRevisionById() {
                console.log(this.value);
                var self = this;
                self.fullscreenLoading = true;
                axios
                    .get("/api/revisions/load_revision_by_id/" + this.value)
                    .then(response => {
                        if (response.data.status == 1) {
                            if (response.data.data.proofs.length > 0) {
                                /*  self.versions = response.data.data.versions; */
                                /* self.current_version_number = response.data.data.version;
                                self.value = response.data.data.version; */
                                self.proofs = [];
                                self.current_review_id = response.data.data.id;
                                response.data.data.proofs.forEach(function (element, index) {
                                    var proof = {
                                        id: element.id,
                                        revision_id: element.revision_id,
                                        project_id: response.data.data.project_id,
                                        active_thumb: false,
                                        picture_url: "/storage/" + element.project_files.path,
                                        thumb_url: "/storage/" + element.project_files.path,
                                        canvas_data: element.canvas_data,
                                        issues: element.issues
                                    };
                                    self.proofs.push(proof);
                                    self.fullscreenLoading = false;
                                });
                                if (self.proofs.length > 0) {
                                    self.loadThumb(0);
                                    self.pen_active = true;
                                }
                            } else {
                                self.fullscreenLoading = false;
                            }
                        } else {
                            this.handle_error(response.data.errors);
                        }
                    })
                    .catch(error => {
                        self.sendLoading = false;
                    });
            },
            handle_error(errors) {
                this.fullscreenLoading = false;
                var text = "Connection Error!";
                if (Object.keys(errors).length > 0) {
                    text = "";
                    for (let index in errors) {
                        text += errors[index] + "\n";
                    }
                }
                swal({
                    position: "center",
                    type: "error",
                    title: "",
                    text: text,
                    showConfirmButton: true
                });
            },
            handlePreview() { },
            handleRemove() { },
            handleError(error) {
            },
            handleSuccess(response) {
                toastr["success"](response.message, "Success");
            },
            applyZoom() {
                var self = this;
                self.background_group.scale(
                    {
                        x: self.zoom_level,
                        y: self.zoom_level
                    }
                );
                self.background_group.getChildren(function (node) {
                    if (node.getClassName() === "Group") {
                        node.getChildren(function (node_1) {
                            if (node_1.getClassName() == 'Circle') {
                                node_1.radius(20 / self.background_group.scaleX());
                                node_1.strokeWidth(2 / self.background_group.scaleX());
                            }
                            if (node_1.getClassName() == 'Text') {
                                node_1.fontSize(15 / self.background_group.scaleX());
                                node_1.offsetX(8 / self.background_group.scaleX());
                                node_1.offsetY(8 / self.background_group.scaleY());
                            }
                        })
                    }
                    return node.getClassName() === "Group";
                });

                self.background.draw();
            },
            handleOpen(key, keyPath) {
                console.log(key, keyPath);
            },
            handleClose(key, keyPath) {
                console.log(key, keyPath);
            },
            jqueryCssAdjust() {
                $('.navbar').css('display', 'none');
                $('.el-slider__bar').css('background-color', 'rgb(216, 218, 223)');
                $('.el-slider__button').css('border-color', '#fa5555');
                $('.el-slider__button').css('width', '12px');
                $('.el-slider__button').css('height', '12px');
                $('.el-slider__runway').css('height', '4px');
                $('.el-slider__bar').css('border-radius', '0px');
                $('.el-slider__runway').css('border-radius', '0px');
                $('.el-slider__button').css('background-color', 'rgb(250, 85, 85)');
                $('.el-radio-button__inner').css('border-radius', '0px');
                $('.el-radio-button__inner').css('border-color', '#dfe4ed');
                $('.el-radio-button__inner').css('color', '#fff');
                $('.el-radio-button__orig-radio:checked + .el-radio-button__inner').css('background-color', 'rgb(224, 223, 223)');
                $('.el-radio-button__orig-radio:checked + .el-radio-button__inner').css('box-shadow', 'none');
                $('.el-radio-button__orig-radio:checked + .el-radio-button__inner').css('color', 'rgb(193, 195, 194)');
                $('.konvajs-content').css('height', $('#content').height);
                $('.el-dialog__header').css('padding-top', '20px');
                $('.el-dialog__body').css('padding-top', '0px');
                $('.el-dialog__body').css('padding-bottom', '10px');
                $('.el-dialog__footer').css('padding-bottom', '10px');
                $('.el-dialog__footer').css('padding-top', '0px');
                $('.el-upload__text').text('Click or drag here to upload files');
                $('.el-upload__input').css('display', 'none')
            },
            alert() {
                this.isCollapse = !this.isCollapse;
            },
            goToC() {

                this.$router.push({ path: '/prooflo/c2' });
            },
            open() {
                this.$alert('This is a message', 'Title', {
                    confirmButtonText: 'OK',
                    callback: action => {
                        this.$message({
                            type: 'info',
                            message: `action: ${action}`
                        });
                    }
                });
            },
            cancelSubmitCorrections() {
                this.showSubmitOptions = false;
            },
            cancelApproveCorrections() {
                this.showApproveDesign = false;
            },
            w3_open() {
                document.getElementById("main").style.marginLeft = "15%";
                document.getElementById("leftSidebar").style.width = "15%";
                document.getElementById("leftSidebar").style.display = "block";
                /*    $('.konvajs-content').css('width', $('#main').width());
                   $('canvas').css('width', $('#main').width()); */

                /* document.getElementById("openNav").style.display = 'none'; */
            },
            w3_close() {
                document.getElementById("main").style.marginLeft = "0%";
                document.getElementById("main").style.width = document.getElementById("leftSidebar").offsetWidth;
                console.log(document.getElementById("stage").offsetWidth);
                /*   $('.konvajs-content').css('width', $('#main').width());
                  $('canvas').css('width', $('#main').width()); */
                document.getElementById("leftSidebar").style.display = "none";
                /*  document.getElementById("openNav").style.display = "inline-block"; */
            },

        },
        watch: {
            showSideBar() {
                if (this.showSideBar == true) {
                    this.w3_open();
                } else {
                    this.w3_close();
                }
            },
            'current_comment.description'() {
                if (this.current_comment.description != '') {
                    this.errors.comment_description = false;
                }
            },
            'editable_issue.description'() {
                if (this.editable_issue.description != '') {
                    this.errors.editable_issue_description = false;
                }
            },
            'current_issue.description'() {
                if (this.new_issue.description != '') {
                    this.errors.current_issue_description = false;
                }
            },
            'editable_comment.description'() {
                if (this.editable_comment.description != '') {
                    this.errors.editable_comment_description = false;
                }
            },
            zoom_level() {
                this.applyZoom();
            },

        },
        computed: {
            ...mapGetters(
                { current_user: 'user', projects: 'projects', current_role: 'current_role' }
            )
        },
        created() {
            /*   console.log(Spark.state.user); */
            this.fullscreenLoading = true;

            var self = this;
            axios.get("/api/auth/getCurrentRole/" + this.project_id)
                .then(response => {
                    if (response.data.status == 1) {
                        self.current_rol = response.data.data;
                        /*  console.log(this.current_rol); */
                        self.loadCollaborators(self.project_id, function () {
                            self.loadInitialRevision(self.project_id, self.revision_id);
                        });
                    } else {
                        self.handle_error(response.data.errors);
                    }
                })
                .catch(error => {
                    self.sendLoading = false;
                });

            this.imageFormData.file_type = "picture";
            this.imageFormData.project_id = this.project_id;
            this.disabled_remove = true;
        },
        mounted() {
            this.$nextTick(function () {
                this.w3_open();
                var container = document.getElementById('stage');
                this.initialStageWidth = container.offsetWidth;
                this.initialStageHeight = container.offsetHeight;
                this.jqueryCssAdjust();

            });

            /*  if (this.current_proofs_state.length > 0) {
                 if (this.proofs.length > 0) {
                     if (this.proofs[0].project_id == this.current_proofs_state[0].project_id) {
                         this.proofs = this.current_proofs_state;
                     }
                 }
             } */

            //this.loadInitialRevision(this.project_id, this.revision_id);

        },
        updated() {
            this.jqueryCssAdjust();
        }

    }
</script>
<style>
    body {
        padding-top: 0px !important;
        overflow-y: hidden;
        overflow-x: hidden;
    }

    .el-radio-button__orig-radio:checked+.el-radio-button__inner {
        background-color: #2196F3;
        border-color: rgb(18, 130, 221);
    }

    #content {
        height: 100% !important
    }

    .icon-center {
        margin-top: 40% !important;
        margin-left: -40% !important;
    }

    .el-row {
       /*  margin-bottom: 20px;
        &:last-child {
            margin-bottom: 0;
        } */
    }

    .top-bar-full {
        box-shadow: 1px 1px 0.1px 0px rgba(0, 0, 0, 0.2);
        margin-bottom: 0px;
    }

    .center-bar-full {
        margin-bottom: 0px;
    }

    .el-col {
        border-radius: 4px;
    }

    .bg-top-bar {
        background-color: #f9f9f9;
    }

    .bg-back-btn {
        background: #fff;
        width: 64px;
        border-right: 1px solid #e6e6e6;
    }

    .bg-purple-light {
        background: #dadcdf;
    }

    .grid-content {
        border-radius: 0px;
        min-height: 60px;
    }

    .row-bg {
        padding: 10px 0;
        background-color: #f9fafc;
    }

    .side-menu-left {
        padding: 10px;
        overflow-y: scroll;
        overflow-x: hidden;
        /* background-color: rgb(92, 81, 194); */
    }

    .side-menu-right {
        height: 100vh;
        overflow-y: scroll;
        overflow-x: hidden;
        border-left: 1px solid #dadbda;
        /* background-color: #044921
 */
    }

    .central-content {
        margin-top: 20px;
    }

    .footer {
        box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.4), 0 6px 20px 0 rgba(0, 0, 0, 0.19);
        background-color: #f9f9f9;
        bottom: 0px;
        position: fixed;
        width: 100%;
        height: 50px;
        padding: 7px;
    }

    .edition-btn {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        text-align: center;
        padding-left: 15px;
        padding-bottom: 10px;
        font-size: 22px;
        border: 1px solid #cacbcc;
        color: #626364;
        outline: none;
        background-color: #fff;
    }

    .edition-btn-active {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        text-align: center;
        padding-left: 15px;
        padding-bottom: 10px;
        font-size: 22px;
        border: none;
        color: #fff;
        outline: none;
        background-color: #fa5555;
    }

    .special-img {
        position: relative;
        top: -5px;
        float: left;
        left: -5px;
    }

    .img-circle {
        border-radius: 50%;
    }

    .current_issue_active {
        border-left: 10px solid #f07f7f;
        min-height: 100px;
        width: 100%;
        background-color: #f07f7f18;
        margin-bottom: 15px;
        padding-bottom: 10px;
    }

    .current_issue_normal {
        border-left: 10px solid #cfcece;
        min-height: 100px;
        width: 100%;
        background-color: #b4afaf2c;
        margin-bottom: 15px;
        padding-bottom: 10px;
    }

    .current_issue_solved {
        border-left: 10px solid #009688;
        min-height: 100px;
        width: 100%;
        background-color: #80b4a052;
        margin-bottom: 15px;
        padding-bottom: 10px;
    }

    .comment {
        border-radius: 10px;
        margin-left: 10px;
        margin-right: 10px;
        width: 100%;
        color: #4d4a4a;
        padding: 10px;
        background-color: #f1f2f2;
        margin-bottom: 5px;
    }

    .active-thumb {
        border: 3px solid #f07f7f;
    }

    .regular-thumb {
        border: 3px solid #c7c5c5;
    }

    .active-thumb-number {
        background-color: #f07f7f;
        border-radius: 0px;
        text-align: center;
    }

    .regular-thumb-number {
        background-color: #727171;
        border-radius: 0px;
        text-align: center;
    }

    #app>div>div.el-dialog__wrapper>div {
        min-width: 300px !important;
    }

    .el-badge__content {
        font-size: 14px;
        height: 25px;
        line-height: 23px;
        text-align: center;
    }

    .issue-button-details {
        background-color: transparent;
        border-color: transparent;
        font-size: 20px;
        color: #4d4a4a;
    }

    .issue-button-edit {
        background-color: transparent;
        border-color: transparent;
        font-size: 20px;
        color: darkgreen;
    }

    .issue-button-solved {
        background-color: transparent;
        border-color: transparent;
        font-size: 20px;
        color: rgb(1, 25, 61);
    }

    .tag {
        background-color: #f07f7f;
        border-color: #fa5555;
        font-size: 16px;
        font-family: Arial, Helvetica, sans-serif;
        color: #fff;
    }

    .tag_solved {
        background-color: #009688;
        border-color: #023f1b;
        font-size: 16px;
        font-family: Arial, Helvetica, sans-serif;
        color: #fff;
    }

    .el-button:hover,
    .el-button:focus {
        color: #80b4a0;
        border-color: transparent;
        background-color: transparent;
    }

    .el-upload-dragger {
        width: 100% !important;
        border-width: 0px !important;
        height: 130px;
    }

    .el-upload-list,
    .el-upload-list--picture {
        width: 100%;
    }

    .el-dialog__body {
        padding-bottom: 10px !important;
        padding-top: 0px;
    }

    #app>div>div:nth-child(7)>div>div.el-dialog__body {
        padding-top: 0px;
    }

    #app>div>div:nth-child(7)>div>div.el-dialog__header {
        padding-top: 0px;
    }

    .el-button--danger:hover,
    .el-button--danger:focus {
        background-color: #fb7777;
        color: #fff;
    }

    .el-radio-button__orig-radio:checked+.el-radio-button__inner {
        background-color: #2196F3;
        border-color: rgb(18, 130, 221);
    }

    .swal2-popup,
    .swal2-title {
        font-size: 20px !important;
    }

    .swal2-content {
        font-size: 16px !important;
    }

    .error_textarea {
        border: 1px solid red;
        border-radius: 4px
    }

    #app>div>div.center-bar-full.el-row>div.el-col.el-col-24.el-col-xs-24.el-col-sm-18.el-col-md-13.el-col-lg-15>div>div.el-row.is-justify-end.el-row--flex>div.el-col.el-col-24.el-col-sm-16>div>div>div.el-input.el-input--suffix>input {
        background-color: #f07f7f;
        border: 0px;
        color: #fff !important;
        font-weight: bold;
        text-align: center;
    }

    ::placeholder {
        /* Chrome, Firefox, Opera, Safari 10.1+ */
        color: #fff !important;
        opacity: 1;
        /* Firefox */
    }

    .el-select .el-input .el-select__caret {
        color: #fff !important;
        opacity: 1;
    }

    .stage-parent {
        width: 100%;
    }

    .w3-card {
        box-shadow: 1px 0px 7px 0px rgba(220, 218, 218, 0.16), 0 2px 10px 0 rgba(0, 0, 0, 0.12)
    }

    #leftSidebar {
        z-index: 300;
    }

    #rightSidebar {
        z-index: 300;
    }

    .fade-in-enter-active,
    .fade-in-leave-active {
        transition: opacity .3s;
    }

    .fade-in-enter,
    .fade-in-leave-to
    /* .fade-leave-active below version 2.1.8 */

        {
        opacity: 0;
    }
    
    ul.collaborators {
        display: inline-block;
        list-style: none;
    }

    ul.collaborators > li {
        float: left;
    }
</style>