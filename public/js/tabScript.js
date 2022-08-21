/* GLOBAL VARIABLE */
  var activitycm,colorcm,stringcm;

  /* Autocomplete View Level */
  var ButtonTags = {
    attrs: {
      'android:id': ["@+id/ "],
      'android:layout_width': ["match_parent", "wrap_content"],
      'android:layout_height': ["match_parent", "wrap_content"],
      'android:gravity': ["fill", "center", "start", "end"],
      'android:text':[" "],
      'android:textColor':[" "],
      'android:textSize':[" "],
      'android:textStyle':["bold", "normal", "italic"],
      'android:background':[" "]
    }
  };

  var EditTextTags = {
    attrs: {
      'android:id': ["@+id/ "],
      'android:layout_width': ["match_parent", "wrap_content"],
      'android:layout_height': ["match_parent", "wrap_content"],
      'android:layout_marginTop': [" "],
      'android:layout_marginBottom': [" "],
      'android:layout_marginRight': [" "],
      'android:layout_marginLeft': [" "],
      'android:gravity': ["fill", "center", "start", "end"],
      'android:hint': [" "],
      'android:text':[" "],
      'android:textColor':[" "],
      'android:textSize':[" "],
      'android:textStyle':["bold", "normal", "italic"],
      'android:inputType':["none", "textEmailAddress", "text", "numberPassword", "number"],
      'android:background':[" "]
    }
  };

  var ImageViewTags = {
    attrs: {
      'android:id': ["@+id/ "],
      'android:layout_width': ["match_parent", "wrap_content"],
      'android:layout_height': ["match_parent", "wrap_content"],
      'android:scaleType': ["center", "fitXY", "fitCenter"],
      'android:src': ["@drawable/ "]
    }
  };

  var ImageButtonTags = {
    attrs: {
      'android:id': ["@+id/ "],
      'android:layout_width': ["match_parent", "wrap_content"],
      'android:layout_height': ["match_parent", "wrap_content"],
      'android:scaleType': ["center", "fitXY", "fitCenter"],
      'android:src': ["@drawable/ "]
    }
  };
  
  var TextViewTags = {
    attrs: {
      'android:id': ["@+id/ "],
      'android:layout_width': ["match_parent", "wrap_content"],
      'android:layout_height': ["match_parent", "wrap_content"],
      'android:layout_marginTop': [" "],
      'android:layout_marginBottom': [" "],
      'android:layout_marginRight': [" "],
      'android:layout_marginLeft': [" "],
      'android:gravity': ["fill", "center", "start", "end"],
      'android:text':[" "],
      'android:textColor':[" "],
      'android:textSize':[" "],
      'android:textStyle':["bold", "normal", "italic"],
      'android:background':[" "]
    }
  };

  var ViewTags = {
    attrs: {
      'android:id': ["@+id/ "],
      'android:layout_width': ["match_parent", "wrap_content"],
      'android:layout_height': ["match_parent", "wrap_content"],
      'android:background':[" "]
    }
  };
  /* Autocomplete View Level */


  /* Autocomplete Layout Level */
  var activityTags = { 
    LinearLayout: {
      attrs: {
        'xmlns:android="http://schemas.android.com/apk/res/android""':[],
        'xmlns:app="http://schemas.android.com/apk/res-auto"':[],
        'android:id': ["@+id/ "],
        'android:layout_width': ["match_parent", "wrap_content"],
        'android:layout_height': ["match_parent", "wrap_content"],
        'android:layout_marginLeft': [" "],
        'android:layout_marginRight': [" "],
        'android:orientation': ["vertical", "horizontal"]
        
      }
    },

    GridLayout: {
      attrs: {
        'xmlns:android="http://schemas.android.com/apk/res/android""':[],
        'xmlns:app="http://schemas.android.com/apk/res-auto"':[],
        'android:id': ["@+id/ "],
        'android:layout_width': ["match_parent", "wrap_content"],
        'android:layout_height': ["match_parent", "wrap_content"]
      }
    },

    RelativeLayout: {
      attrs: {
        'xmlns:android="http://schemas.android.com/apk/res/android""':[],
        'xmlns:app="http://schemas.android.com/apk/res-auto"':[],
        'android:id': ["@+id/ "],
        'android:layout_width': ["match_parent", "wrap_content"],
        'android:layout_height': ["match_parent", "wrap_content"]
      }
    },

    "androidx.constraintlayout.widget.ConstraintLayout": {
      attrs: {
        'xmlns:android="http://schemas.android.com/apk/res/android""':[],
        'xmlns:app="http://schemas.android.com/apk/res-auto"':[],
        'android:id': ["@+id/ "],
        'android:layout_width': ["match_parent", "wrap_content"],
        'android:layout_height': ["match_parent", "wrap_content"]
      }
    },

    // link to View level
    Button: ButtonTags, ImageView:ImageViewTags, ImageButton:ImageButtonTags, TextView: TextViewTags, EditTextView: EditTextTags, View:ViewTags
  };
  /* Autocomplete Layout Level */


/* Create CodeMirror TextEditor */
  function textEditorCM(){
      var mainactivityTextarea = document.getElementById('MainActivity');
      var colorTextarea = document.getElementById('Color');
      var stringTextarea = document.getElementById('String');
      
      // Replace MainActivity TextArea With Code Mirror
      activitycm = CodeMirror.fromTextArea(mainactivityTextarea, {
          lineNumbers: true,
          styleActiveLine: true,
          mode: 'xml',
          theme: 'dracula',
          smartIndent: false,
          matchTags: {bothTags: true},
          autoCloseTags: true,
          extraKeys: {
            "'<'": completeAfter,
            "'/'": completeIfAfterLt,
            "'='": completeIfInTag
          },
          hintOptions: {schemaInfo: activityTags}
      });
      activitycm.on('change', function(){if(localStorage.dataChanged != 'true'){localStorage.dataChanged = 'true'}activitycm.save();});
      activitycm.on("keyup", function (cm, event) { // Active autocomplete when typing 
        if (!cm.state.completionActive && 
            (event.keyCode > 64 && event.keyCode < 91) || 
            (event.keyCode > 96 && event.keyCode < 123)
           ) {
            CodeMirror.commands.autocomplete(cm, null, {completeSingle: false});
        }
      });
      // Set default value for activity.xml
      activitycm.setValue(`<?xml version="1.0" encoding="utf-8"?>
<LinearLayout xmlns:android="http://schemas.android.com/apk/res/android"
    android:layout_width="match_parent"
    android:layout_height="match_parent">
    
</LinearLayout>`);

      // Replace Color TextArea With Code Mirror
      colorcm = CodeMirror.fromTextArea(colorTextarea, {
        lineNumbers: true,
        styleActiveLine: true,
        mode: 'text/html',
        theme: 'dracula',
        smartIndent: false,
        matchTags: {bothTags: true},
        autoCloseTags: true
      });
      colorcm.on('change', function(){if(localStorage.dataChanged != 'true'){localStorage.dataChanged = 'true'}colorcm.save();});
      colorcm.setValue(`<?xml version="1.0" encoding="utf-8"?>
<resources>
    <color name="colorPrimary">#6200EE</color>
    <color name="colorPrimaryDark">#3700B3</color>
    <color name="colorAccent">#03DAC5</color>
</resources>`);

      // Replace String TextArea With Code Mirror
      stringcm = CodeMirror.fromTextArea(stringTextarea, {
        lineNumbers: true,
        styleActiveLine: true,
        mode: 'text/html',
        theme: 'dracula',
        smartIndent: false,
        matchTags: {bothTags: true},
        autoCloseTags: true
      });
      stringcm.on('change', function(){if(localStorage.dataChanged != 'true'){localStorage.dataChanged = 'true'}stringcm.save();});
      stringcm.setValue(`<resources>
    <string name="app_name">Linear Layout</string>
</resources>`);
  }


/* for logical XML tab click */
  function openTab(tabName,tabNo) {
    var i;
    var x = document.getElementsByClassName("CodeMirror");
    var z = document.getElementsByClassName("tab-box");
    for (i = 0; i < x.length; i++) {
      x[i].style.display = "none";  
      z[i].className="btn btn-secondary tab-box rounded-0 font-italic";
    }
    document.getElementsByClassName("CodeMirror")[tabNo].style.display = "block";
    document.getElementById(tabName).className="btn btn-dark tab-box rounded-0";
  }


/* Ask when click submit button */
  function submitbutton(){
    var check = confirm("Are you sure you want to submit this task?");
    if(check == true){
      localStorage.submitClick = 'true';
    }else{
    localStorage.submitClick = 'false';
      return false;
    }
  }


/* Rotation button */
  function orientationbutton(){
    if(localStorage.orientation == 0){
      document.getElementById("right-panel").className = "col-md-12";
      document.getElementById("left-panel").className = "col-md-12";
      localStorage.orientation = 1;
    }else{
      document.getElementById("right-panel").className = "col-md-6";
      document.getElementById("left-panel").className = "col-md-6";
      localStorage.orientation = 0;
    }
  }

/* Warning when reload (unused) */
  // window.addEventListener("beforeunload", function(event) {
  //   if(localStorage.dataChanged == 'true'){
  //     event.returnValue = 'not saved!';
  //   }
  // });

/* Auto Complete Function */
  function completeAfter(cm, pred) {
    var cur = cm.getCursor();
    if (!pred || pred()) setTimeout(function() {
      if (!cm.state.completionActive)
        cm.showHint({completeSingle: false});
    }, 100);
    return CodeMirror.Pass;
  }

/* Auto Complete Function */
  function completeIfAfterLt(cm) {
    return completeAfter(cm, function() {
      var cur = cm.getCursor();
      return cm.getRange(CodeMirror.Pos(cur.line, cur.ch - 1), cur) == "<";
    });
  }

/* Auto Complete Function */
  function completeIfInTag(cm) {
    return completeAfter(cm, function() {
      var tok = cm.getTokenAt(cm.getCursor());
      if (tok.type == "string" && (!/['"]/.test(tok.string.charAt(tok.string.length - 1)) || tok.string.length == 1)) return false;
      var inner = CodeMirror.innerMode(cm.getMode(), tok.state).state;
      return inner.tagName;
    });
  }

/* Auto Complete Function */
function replaceCode(activity, colors, strings) {
  var check = confirm("Warning! data that you have not submitted will be lost! Are you sure want to re-open this state?");
    if(check == true){
      activitycm.setValue(activity);
      colorcm.setValue(colors);
      stringcm.setValue(strings);
    }else{
      return false;
  }
}

/*THIS BLOCK RUN FIRST 'Like Main()' */
  window.addEventListener('DOMContentLoaded', function(e) {
    this.textEditorCM();
    this.openTab('MainActivityTab','0');
    localStorage.submitClick = 'false';
    localStorage.dataChanged = 'false';
  });