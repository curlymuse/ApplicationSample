export default {

    props: {
        status: {
            type: Boolean,
            default: false,
            required: true
        },
        message: {
            type: String,
            default: "",
            required: true
        },
        type: {
            type: String,
            default: "Error",
            required: true
        },
        time: {
            type: Number,
            default: 3000
        }
    },

    data () {
        return {
            toasterType: {
                success: {
                    heading: 'Success',
                    class: 'primary'
                },
                error: {
                    heading: 'Error',
                    class: 'danger'
                },
                info: {
                    heading: 'Info',
                    class: 'info'
                },
                warning: {
                    heading: 'Warning',
                    class: 'warning'
                }
            }
        }
    },

    watch : {
        status (val) {
            if (val === true) {
                setTimeout(() => {
                    this.status = false;
                }, this.time);
            }
        }
    },

    template :  `<div class="animated" v-show="status" transition="toaster">
                    <div class="panel panel-{{toasterType[type].class}} toaster-{{type}}">
                        <div class="panel-heading" :style="styleObjectHeading">
                            {{toasterType[type].heading | capitalize}}: {{message}}
                        </div>
                    </div>
                </div>`
};
