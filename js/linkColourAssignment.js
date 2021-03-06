var CLMSUI = CLMSUI || {};
CLMSUI.linkColour = CLMSUI.linkColour || {};

CLMSUI.BackboneModelTypes.ColourModel = Backbone.Model.extend ({
    defaults: {
        title: undefined,
    },
    setDomain : function (twoValArr) {
        this.get("colScale").domain(twoValArr);
        this.trigger ("colourModelChanged", twoValArr);
        if (this.collection) {
            this.collection.trigger ("aColourModelChanged", this, twoValArr);
        }
    }
});

CLMSUI.BackboneModelTypes.ColourModelCollection = Backbone.Collection.extend ({
    model: CLMSUI.BackboneModelTypes.ColourModel,
});


CLMSUI.BackboneModelTypes.DefaultColourModel = CLMSUI.BackboneModelTypes.ColourModel.extend ({
    initialize: function () {
        this.set("labels", this.get("colScale").copy().range(["Self-Link", "Homomultimer Link", "Inter-Protein Link"]));
    },
    getColour: function (crossLink) {
        return this.get("colScale")(crossLink.isSelfLink() || crossLink.toProtein === null ? (crossLink.confirmedHomomultimer ? 1 : 0) : 2);
    },
});


CLMSUI.BackboneModelTypes.GroupColourModel = CLMSUI.BackboneModelTypes.ColourModel.extend ({
    initialize: function (attrs, options) {
        
        this.searchMap = options.searchMap;
        //put d3.scale for group colour assignment in compositeModel
        var groups = new Map();
        for (var search of this.searchMap) {
            var val = search[1];
            var arr = groups.get(val.group);
            if (!arr) {
                arr = [];
                groups.set (val.group, arr);
            }
            arr.push (val.id);
        }

        var groupDomain = [undefined];
        var labelRange = ["Multiple Group"];
        for (var group of groups) {
            groupDomain.push (group[0]);
            labelRange.push ("Group "+group[0]+" ("+group[1].join(", ")+")");
        }
         var groupCount = groups.size;
        var colScale;

        if (groupCount < 6) {
            var colArr = ["grey"].concat(colorbrewer.Dark2[5]);
            colScale = d3.scale.ordinal().range(colArr).domain(groupDomain);
        } else if (groupCount < 11) {
            var colArr = ["grey"].concat(colorbrewer.Paired[10]);
            colScale = d3.scale.ordinal().range(colArr).domain(groupDomain);
        } else { // more than 10 groups, not really feasible to find colour scale that works
                //a d3.scale that always returns gray?
            colScale = d3.scale.linear().domain([-1,0]).range(["grey", "#448866"]).clamp(true);
            labelRange = ["Multiple Group", "Single Group"];
        }
        this.set("colScale", colScale);
        this.set("labels", this.get("colScale").copy().range(labelRange));
    },
    getColour: function (crossLink) {	
         //check number of groups to choose appropriate colour scheme,
        // (only do once)
        //check if link uniquely belongs to one group
        var groupCheck = d3.set();
        for (var match_pp of crossLink.filteredMatches_pp) {
            var match = match_pp.match; 
            var group = this.searchMap.get(match.searchId).group; 	
            groupCheck.add(group);
        }
        var scale = this.get("colScale");
        // choose value or right unknown value (linear scales don't do undefined)
        var value = (groupCheck.values().length === 1 ? groupCheck.values()[0] : (scale.domain()[0] === -1 ? -1 : undefined));
        return scale (value);		
    },
});

CLMSUI.BackboneModelTypes.DistanceColourModel = CLMSUI.BackboneModelTypes.ColourModel.extend ({
    initialize: function () {
        this.set("labels", this.get("colScale").copy().range(["Short", "Average", "Overlong"]));
    },
    getDistance : function (crossLink) {
        return CLMSUI.compositeModelInst.getSingleCrosslinkDistance (crossLink);
    },
    getColour: function (crossLink) {
        return this.get("colScale")(this.getDistance (crossLink));
    },
});


CLMSUI.linkColour.setupColourModels = function () {
    CLMSUI.linkColour.defaultColoursBB = new CLMSUI.BackboneModelTypes.DefaultColourModel ({
        colScale: d3.scale.ordinal().domain([0,1,2]).range([
            CLMS.xiNET.defaultSelfLinkColour.toRGB(), CLMS.xiNET.homodimerLinkColour.toRGB(), CLMS.xiNET.defaultInterLinkColour.toRGB()
        ]),
        title: "Default",
    });
    
    CLMSUI.linkColour.groupColoursBB = new CLMSUI.BackboneModelTypes.GroupColourModel ({
        title: "Group",
    }, {
        searchMap: CLMSUI.compositeModelInst.get("clmsModel").get("searches"),
    });
    
    CLMSUI.linkColour.distanceColoursBB = new CLMSUI.BackboneModelTypes.DistanceColourModel ({
        colScale: d3.scale.threshold().domain([0,1]).range(['#5AAE61','#FDB863','#9970AB']),
        //colScale: d3.scale.threshold().domain([0,1]).range(['#daa', '#ddd', '#aad']),
        title: "Distance",
    });

    // add distanceColoursBB to this collection later if needed
    CLMSUI.linkColour.Collection = new CLMSUI.BackboneModelTypes.ColourModelCollection ([
        CLMSUI.linkColour.defaultColoursBB,
        CLMSUI.linkColour.groupColoursBB,
        CLMSUI.linkColour.distanceColoursBB,
    ]);
};
