<template>
    <div class="row">
        <div class="col-md-12">

            <div class="d-flex flex-wrap mb-4">
                <div class="mr-auto">
                   <div class="d-flex">
                        <div>
                            <span class="text-title"> Dashboard </span>
                        </div>
                    </div>
                </div>
                <div class="">
                    <date-picker type="month" :lang='date.lang' :format="date.format" v-model="dashboard_month" @change="dashboard_month_change" input-class="form-control bg-white"></date-picker>
                </div>
            </div>
            
            <div class="d-flex flex-wrap mb-4">
                <div class="col-md-12">
                    <div class="row">
                        
                        <div class="d-flex align-items-start flex-column p-1 mb-1 col-md-3">
                            <div class="col-12 p-3 bordered bg-light">
                                <div class="d-flex align-items-end flex-column box-content">
                                    <div class="p-2">Total Orders</div>

                                    <div class="mt-auto p-2">
                                        <span class="text-headline">
                                            <i class='fa fa-circle-notch fa-spin' v-if="order_count.processing == true"></i>
                                            <span v-else>{{ order_count.value }}</span>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex align-items-start flex-column p-1 mb-1 col-md-3 box">
                            <div class="col-12 p-3 bordered bg-light">
                                <div class="d-flex align-items-end flex-column box-content">
                                    <div class="p-2">Total Order Value</div>

                                    <div class="mt-auto p-2">
                                        <span class="text-headline">
                                            <i class='fa fa-circle-notch fa-spin' v-if="order_value.processing == true"></i>
                                            <span v-else>{{ order_value.value }}</span>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex align-items-start flex-column p-1 mb-1 col-md-3 box">
                            <div class="col-12 p-3 bordered bg-light">
                                <div class="d-flex align-items-end flex-column box-content">
                                    <div class="p-2">Total Revenue</div>

                                    <div class="mt-auto p-2">
                                        <span class="text-headline">
                                            <i class='fa fa-circle-notch fa-spin' v-if="revenue_value.processing == true"></i>
                                            <span v-else>{{ revenue_value.value }}</span>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex align-items-start flex-column p-1 mb-1 col-md-3 box">
                            <div class="col-12 p-3 bordered bg-light">
                                <div class="d-flex align-items-end flex-column box-content">
                                    <div class="p-2">Total Customers</div>

                                    <div class="mt-auto p-2">
                                        <span class="text-headline">
                                            <i class='fa fa-circle-notch fa-spin' v-if="customer_count.processing == true"></i>
                                            <span v-else>{{ customer_count.value }}</span>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="d-flex align-items-start flex-column p-1 mb-1 col-md-3 box">
                            <div class="col-12 p-3 bordered bg-light">
                                <div class="d-flex align-items-end flex-column box-content">
                                    <div class="p-2">Total Expense</div>

                                    <div class="mt-auto p-2">
                                        <span class="text-headline">
                                            <i class='fa fa-circle-notch fa-spin' v-if="purchase_value.processing == true"></i>
                                            <span v-else>{{ purchase_value.value }}</span>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex align-items-start flex-column p-1 mb-1 col-md-3 box">
                            <div class="col-12 p-3 bordered bg-light">
                                <div class="d-flex align-items-end flex-column box-content">
                                    <div class="p-2">Net profit</div>

                                    <div class="mt-auto p-2">
                                        <span class="text-headline">
                                            <i class='fa fa-circle-notch fa-spin' v-if="net_profit_value.processing == true"></i>
                                            <span v-else>{{ net_profit_value.value }}</span>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- <div class="d-flex align-items-start flex-column p-1 mb-1 col-md-3 box">
                            <div class="col-12 p-3 bordered bg-light">
                                <div class="d-flex align-items-end flex-column box-content">
                                    <div class="p-2">Total Suppliers</div>

                                    <div class="mt-auto p-2">
                                        <span class="text-headline">
                                            <i class='fa fa-circle-notch fa-spin' v-if="supplier_count.processing == true"></i>
                                            <span v-else>{{ supplier_count.value }}</span>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div> -->

                        <div class="d-flex align-items-start flex-column p-1 mb-1 col-md-3 box">
                            <div class="col-12 p-3 bordered bg-light">
                                <div class="d-flex align-items-end flex-column box-content">
                                    <div class="p-2">Total Products</div>

                                    <div class="mt-auto p-2">
                                        <span class="text-headline">
                                            <i class='fa fa-circle-notch fa-spin' v-if="product_count.processing == true"></i>
                                            <span v-else>{{ product_count.value }}</span>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex align-items-start flex-column p-1 mb-1 col-md-3 box">
                            <div class="col-12 p-3 bordered bg-light">
                                <div class="d-flex align-items-end flex-column box-content">
                                    <div class="p-2">Total Users</div>

                                    <div class="mt-auto p-2">
                                        <span class="text-headline">
                                            <i class='fa fa-circle-notch fa-spin' v-if="user_count.processing == true"></i>
                                            <span v-else>{{ user_count.value }}</span>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
            

            <div class="d-flex flex-wrap mb-4">
                <div class="mr-auto">
                   <div class="d-flex">
                        <div>
                            <span class="text-subtitle"> Order Count Activity</span>
                        </div>
                    </div>
                </div>
                <div class="">
                    
                </div>
            </div>
            <div class="d-flex align-items-start flex-column p-1 mb-1">
                <div class='order_count_activity_chart_container'> 
                    <canvas id="order_count_activity_chart"></canvas>
                </div>
            </div>

            <div class="d-flex flex-wrap mb-4">
                <div class="mr-auto">
                   <div class="d-flex">
                        <div>
                            <span class="text-subtitle"> Order Revenue Activity</span>
                        </div>
                    </div>
                </div>
                <div class="">
                    
                </div>
            </div>
            <div class="d-flex align-items-start flex-column p-1 mb-1">
                <div class='order_revenue_activity_chart_container'> 
                    <canvas id="order_revenue_activity_chart"></canvas>
                </div>
            </div>

        </div>
    </div>
</template>  

<script>

    'use strict';
    
    import DatePicker from 'vue2-datepicker';
    import Chart from 'chart.js';
    import moment from "moment";
    
    export default {
        data(){
            return{
                date:{
                    lang : 'en',
                    format : "YYYY-MM",
                },

                dashboard_month : new Date(moment().format("YYYY-MM")),
                dashboard_month_formatted : new Date(moment().format("YYYY-MM")),

                order_count : {
                    processing : false,
                    value : 0
                },

                order_value : {
                    processing : false,
                    value : 0
                },

                revenue_value : {
                    processing : false,
                    value : 0
                },

                customer_count: {
                    processing : false,
                    value : 0
                },

                product_count: {
                    processing : false,
                    value : 0
                },

                supplier_count: {
                    processing : false,
                    value : 0
                },
                 
                store_count: {
                    processing : false,
                    value : 0
                },

                user_count: {
                    processing : false,
                    value : 0
                },

                purchase_value : {
                    processing : false,
                    value : 0
                },

                net_profit_value : {
                    processing : false,
                    value : 0
                },

                chart_objects : {},

                chart_config: {
                    'order_count_activity_chart': {
                        type: 'line',
                        data: {
                            datasets: [{
                                borderWidth: 1,
                                fill: true,
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            lineTension: 1,
                            scales: {
                                yAxes: [{
                                    ticks: {
                                        beginAtZero: true,
                                        padding: 25,
                                    }
                                }]
                            }
                        }   
                    },
                    'order_revenue_activity_chart': {
                        type: 'line',
                        data: {
                            datasets: [{
                                borderWidth: 1,
                                fill: true,
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            lineTension: 1,
                            scales: {
                                yAxes: [{
                                    ticks: {
                                        beginAtZero: true,
                                        padding: 25,
                                    }
                                }]
                            }
                        }   
                    }
                },
                
                chart_settings : {
                    'order_count_activity_chart': {
                        'fill_colour': 'rgb(0, 123, 255, 0.6)',
                        'dot_colour': '#007BFE',
                        'title': {
                            'top': 'No of Orders Per Day (Monthly)',
                            'x_axis': 'Date',
                            'y_axis': 'No of Orders'
                        }
                    },
                    'order_revenue_activity_chart': {
                        'fill_colour': 'rgb(38, 167, 69, 0.6)',
                        'dot_colour': '#28a745',
                        'title': {
                            'top': 'Order Revenue Per Day (Monthly)',
                            'x_axis': 'Date',
                            'y_axis': 'Revenue'
                        }
                    }
                }
            }
        },
        props: {
            
        },
        mounted() {
            console.log('Dashboard loaded');
            this.fire_requests();
        },
        methods: {

            convert_date_format(date){
                return (date != '')?moment(date).format("YYYY-MM"):'';
            },

            dashboard_month_change(){
                this.dashboard_month_formatted = this.convert_date_format(this.dashboard_month);
                this.fire_requests();
            },

            set_form_data(){
                var formData = new FormData();
                formData.append("access_token", window.settings.access_token);
                formData.append("date", this.convert_date_format(this.dashboard_month_formatted));
                return formData;
            },

            fire_requests(){
                
                this.count_order();
                this.count_order_value();
                this.count_total_revenue();
                this.count_customers();
                this.count_products();
                //this.count_suppliers();
                //this.count_stores();
                this.count_users();
                this.count_purchases();
                this.count_net_profit();

                this.order_count_activity_chart();
                this.order_revenue_activity_chart();
            },

            count_order(){
                var formData = this.set_form_data();

                this.order_count.processing = true;

                axios.post('/api/count_orders', formData).then((response) => {
                    this.order_count.processing = false;
                    if(response.data.status_code == 200) {
                        this.order_count.value = response.data.data.count_formatted;
                    }else{
                        this.order_count.value = '-';
                    }
                })
                .catch((error) => {
                    console.log(error);
                });
            },

            count_order_value(){
                var formData = this.set_form_data();
                
                this.order_value.processing = true;

                axios.post('/api/count_order_value', formData).then((response) => {
                    this.order_value.processing = false;
                    if(response.data.status_code == 200) {
                        this.order_value.value = response.data.data.count_formatted;
                    }else{
                        this.order_value.value = '-';
                    }
                })
                .catch((error) => {
                    console.log(error);
                });
            },

            count_total_revenue(){
                var formData = this.set_form_data();
                
                this.revenue_value.processing = true;

                axios.post('/api/count_total_revenue', formData).then((response) => {
                    this.revenue_value.processing = false;
                    if(response.data.status_code == 200) {
                        this.revenue_value.value = response.data.data.count_formatted;
                    }else{
                        this.revenue_value.value = '-';
                    }
                })
                .catch((error) => {
                    console.log(error);
                });
            },

            count_customers(){
                var formData = this.set_form_data();
                
                this.customer_count.processing = true;

                axios.post('/api/count_customers', formData).then((response) => {
                    this.customer_count.processing = false;
                    if(response.data.status_code == 200) {
                        this.customer_count.value = response.data.data.count_formatted;
                    }else{
                        this.customer_count.value = '-';
                    }
                })
                .catch((error) => {
                    console.log(error);
                });
            },

            count_products(){
                var formData = this.set_form_data();
                
                this.product_count.processing = true;

                axios.post('/api/count_products', formData).then((response) => {
                    this.product_count.processing = false;
                    if(response.data.status_code == 200) {
                        this.product_count.value = response.data.data.count_formatted;
                    }else{
                        this.product_count.value = '-';
                    }
                })
                .catch((error) => {
                    console.log(error);
                });
            },

            count_suppliers(){
                var formData = this.set_form_data();
                
                this.supplier_count.processing = true;

                axios.post('/api/count_suppliers', formData).then((response) => {
                    this.supplier_count.processing = false;
                    if(response.data.status_code == 200) {
                        this.supplier_count.value = response.data.data.count_formatted;
                    }else{
                        this.supplier_count.value = '-';
                    }
                })
                .catch((error) => {
                    console.log(error);
                });
            },

            count_stores(){
                var formData = this.set_form_data();
                
                this.store_count.processing = true;

                axios.post('/api/count_stores', formData).then((response) => {
                    this.store_count.processing = false;
                    if(response.data.status_code == 200) {
                        this.store_count.value = response.data.data.count_formatted;
                    }else{
                        this.store_count.value = '-';
                    }
                })
                .catch((error) => {
                    console.log(error);
                });
            },

            count_users(){
                var formData = this.set_form_data();
                
                this.user_count.processing = true;

                axios.post('/api/count_users', formData).then((response) => {
                    this.user_count.processing = false;
                    if(response.data.status_code == 200) {
                        this.user_count.value = response.data.data.count_formatted;
                    }else{
                        this.user_count.value = '-';
                    }
                })
                .catch((error) => {
                    console.log(error);
                });
            },

            order_count_activity_chart(){

                var formData = this.set_form_data();

                axios.post('/api/get_monthly_order_count', formData).then((response) => {
                    if(response.data.status_code == 200) {
                        var chart_object = this.create_chart("order_count_activity_chart", this.chart_config.order_count_activity_chart);
                        this.add_chart_data(
                            chart_object,
                            response.data.data.x_axis,
                            response.data.data.y_axis,
                            this.chart_settings.order_count_activity_chart.title.top,
                            this.chart_settings.order_count_activity_chart.fill_colour,
                            {
                                'x_axis':this.chart_settings.order_count_activity_chart.title.x_axis,
                                'y_axis': this.chart_settings.order_count_activity_chart.title.y_axis
                            }
                        );
                    }else{
                        
                    }
                })
                .catch((error) => {
                    console.log(error);
                });
            },

            order_revenue_activity_chart(){

                var formData = this.set_form_data();

                axios.post('/api/get_monthly_order_revenue', formData).then((response) => {
                    if(response.data.status_code == 200) {
                        var chart_object = this.create_chart("order_revenue_activity_chart", this.chart_config.order_revenue_activity_chart);
                        this.add_chart_data(
                            chart_object,
                            response.data.data.x_axis,
                            response.data.data.y_axis,
                            this.chart_settings.order_revenue_activity_chart.title.top,
                            this.chart_settings.order_revenue_activity_chart.fill_colour,
                            {
                                'x_axis':this.chart_settings.order_revenue_activity_chart.title.x_axis,
                                'y_axis': this.chart_settings.order_revenue_activity_chart.title.y_axis
                            }
                        );
                    }else{
                        
                    }
                })
                .catch((error) => {
                    console.log(error);
                });
            },

            create_chart(canvas_id, chart_data) {
                var ctx = document.getElementById(canvas_id);
                var chart = new Chart(ctx, {
                    type: chart_data.type,
                    data: chart_data.data,
                    options: chart_data.options,
                });
                return chart;
            },

            add_chart_data(chart, label, data, title = '', fill_colour = '', axis_titles = {}) {
                chart.data.labels = label;
                chart.data.datasets.forEach((dataset) => {
                    dataset.data = data;
                    if(title != ''){
                        dataset.label = title;
                    }
                    if(fill_colour != ''){
                        dataset.backgroundColor = fill_colour;
                    }
                });
                if(axis_titles.x_axis != ''){
                    chart.options.scales.xAxes.forEach((xaxis) => {
                        xaxis.scaleLabel.labelString = axis_titles.x_axis;
                        xaxis.scaleLabel.display = true;
                    });
                }
                if(axis_titles.y_axis != ''){
                    chart.options.scales.yAxes.forEach((yaxis) => {
                        yaxis.scaleLabel.labelString = axis_titles.y_axis;
                        yaxis.scaleLabel.display = true;
                    });
                }
                chart.update();
            },

            count_purchases(){
                var formData = this.set_form_data();
                
                this.purchase_value.processing = true;

                axios.post('/api/count_purchases', formData).then((response) => {
                    this.purchase_value.processing = false;
                    if(response.data.status_code == 200) {
                        this.purchase_value.value = response.data.data.count_formatted;
                    }else{
                        this.purchase_value.value = '-';
                    }
                })
                .catch((error) => {
                    console.log(error);
                });
            },

            count_net_profit(){
                var formData = this.set_form_data();
                
                this.net_profit_value.processing = true;

                axios.post('/api/count_net_profit', formData).then((response) => {
                    this.net_profit_value.processing = false;
                    if(response.data.status_code == 200) {
                        this.net_profit_value.value = response.data.data.count_formatted;
                    }else{
                        this.net_profit_value.value = '-';
                    }
                })
                .catch((error) => {
                    console.log(error);
                });
            }
        }
    }
</script>