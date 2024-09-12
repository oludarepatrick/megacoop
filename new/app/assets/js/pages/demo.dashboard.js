!(function (o) {
    "use strict";
    function e() {
        (this.$body = o("body")), (this.charts = []);
    }
    (e.prototype.initCharts = function () {
        window.Apex = {
            chart: { parentHeightOffset: 0, toolbar: { show: !1 } },
            grid: { padding: { left: 0, right: 0 } },
            colors: ["#727cf5", "#0acf97", "#fa5c7c", "#ffbc00"],
        };
        var e = ["#727cf5", "#0acf97", "#fa5c7c", "#ffbc00"];
        (r = o("#revenue-chart").data("colors")) && (e = r.split(","));
        var t = {
            chart: {
                height: 364,
                type: "line",
                dropShadow: { enabled: !0, opacity: 0.2, blur: 7, left: -7, top: 7 },
            },
            dataLabels: { enabled: !1 },
            stroke: { curve: "smooth", width: 4 },
            series: [
                { name: "Savings Balance", data: [cs.JAN, cs.FEB, cs.MAR, cs.APR, cs.MAY, cs.JUN, cs.JUL,cs.AUG, cs.SEP, cs.OCT, cs.NOV, cs.DEC] },
                { name: "Liquidity", data: [cl.JAN, cl.FEB, cl.MAR, cl.APR, cl.MAY, cl.JUN, cl.JUL,cl.AUG, cl.SEP, cl.OCT, cl.NOV, cl.DEC] },
            ],
            colors: e,
            zoom: { enabled: !1 },
            legend: { show: !1 },
            xaxis: {
                type: "string",
                categories: [
                    "Jan",
                    "Feb",
                    "Mar",
                    "Apr",
                    "May",
                    "Jun",
                    "Jul",
                    "Aug",
                    "Sep",
                    "Oct",
                    "Nov",
                    "Dec",
                ],
                tooltip: { enabled: !1 },
                axisBorder: { show: !1 },
            },
            yaxis: {
                labels: {
                    formatter: function (e) {
                        if(e <= 999999){
                             return e/1000 + "k";
                        }
                        if(e > 999999){
                             return e/1000000 + "m";
                        }
                       return e;
                       
                    },
                    offsetX: -15,
                },
            },
        };
        new ApexCharts(document.querySelector("#revenue-chart"), t).render();
        e = ["#727cf5", "#e3eaef", "#727cf5"];
        (r = o("#high-performing-product").data("colors")) && (e = r.split(","));
        t = {
            chart: { height: 278, type: "bar", stacked: !1 },
            plotOptions: { bar: { horizontal: !1, columnWidth: "20%" } },
            dataLabels: { enabled: !1 },
            stroke: { show: !0, width: !0, colors: ["transparent"] },
            series: [
                {
                    name: "Loan",
                    data: [ld.JAN, ld.FEB, ld.MAR, ld.APR, ld.MAY, ld.JUN, ld.JUL,ld.AUG, ld.SEP, ld.OCT, ld.NOV, ld.DEC],
                },
                {
                    name: "Savings",
                    data: [sd.JAN, sd.FEB, sd.MAR, sd.APR, sd.MAY, sd.JUN, sd.JUL,sd.AUG, sd.SEP, sd.OCT, sd.NOV, sd.DEC],
                },
                {
                    name: "Withdrawal",
                    data: [wd.JAN, wd.FEB, wd.MAR, wd.APR, wd.MAY, wd.JUN, wd.JUL,wd.AUG, wd.SEP, wd.OCT, wd.NOV, wd.DEC],
                },
            ],
            zoom: { enabled: !1 },
            legend: { show: !1 },
            colors: e,
            xaxis: {
                categories: [
                    "Jan",
                    "Feb",
                    "Mar",
                    "Apr",
                    "May",
                    "Jun",
                    "Jul",
                    "Aug",
                    "Sep",
                    "Oct",
                    "Nov",
                    "Dec",
                ],
                axisBorder: { show: !1 },
            },
            yaxis: {
                labels: {
                    formatter: function (e) {
                        return e ;
                    },
                    offsetX: -15,
                },
            },
            fill: { opacity: 1 },
            tooltip: {
                y: {
                    formatter: function (e) {
                        return + e; 
                    },
                },
            },
        };
        new ApexCharts(
            document.querySelector("#high-performing-product"),
            t
        ).render();

        var r;
        e = ["#727cf5", "#0acf97", "#fa5c7c", "#ffbc00", "#323A46", "#35B8E0"];
        (r = o("#average-sales").data("colors")) && (e = r.split(","));
        t = {
            chart: { height: 213, type: "donut" },
            legend: { show: !1 },
            stroke: { colors: ["transparent"] },
            series: [overview_data.savings_bal , overview_data.loan_interest, overview_data.reg_fee, overview_data.credit_sales_interest, 
                overview_data.loan_bal, overview_data.credit_sales_bal],
            labels: ["Savings Balance", "Loan Interest", "Registration Fee", "Credit Sales Interest", "Loan Balance", "Credit Sales"],
            colors: e,
            responsive: [
                {
                    breakpoint: 480,
                    options: { chart: { width: 300 }, legend: { position: "bottom" } },
                },
            ],
        };
        new ApexCharts(document.querySelector("#average-sales"), t).render();
    
    }),
        (e.prototype.initMaps = function () {
            0 < o("#world-map-markers").length &&
                o("#world-map-markers").vectorMap({
                    map: "world_mill_en",
                    normalizeFunction: "polynomial",
                    hoverOpacity: 0.7,
                    hoverColor: !1,
                    regionStyle: { initial: { fill: "#e3eaef" } },
                    markerStyle: {
                        initial: {
                            r: 9,
                            fill: "#727cf5",
                            "fill-opacity": 0.9,
                            stroke: "#fff",
                            "stroke-width": 7,
                            "stroke-opacity": 0.4,
                        },
                        hover: { stroke: "#fff", "fill-opacity": 1, "stroke-width": 1.5 },
                    },
                    backgroundColor: "transparent",
                    markers: [
                        { latLng: [40.71, -74], name: "New York" },
                        { latLng: [37.77, -122.41], name: "San Francisco" },
                        { latLng: [-33.86, 151.2], name: "Sydney" },
                        { latLng: [1.3, 103.8], name: "Singapore" },
                    ],
                    zoomOnScroll: !1,
                });
        }),
        (e.prototype.init = function () {
            o("#dash-daterange").daterangepicker({ singleDatePicker: !0 }),
                this.initCharts(),
                this.initMaps();
        }),
        (o.Dashboard = new e()),
        (o.Dashboard.Constructor = e);
})(window.jQuery),
    (function (t) {
        "use strict";
        t(document).ready(function (e) {
            t.Dashboard.init();
        });
    })(window.jQuery);

