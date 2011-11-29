// -------------------------------------------------------------------
// markItUp!
// -------------------------------------------------------------------
// Copyright (C) 2008 Jay Salvat
// http://markitup.jaysalvat.com/
// -------------------------------------------------------------------
// Textile tags example
// http://en.wikipedia.org/wiki/Textile_(markup_language)
// http://www.textism.com/
// -------------------------------------------------------------------
// Feel free to add more tags
// -------------------------------------------------------------------
mySettings = {
	onShiftEnter:		{keepDefault:false, replaceWith:'\n\n'},
  previewParserPath:   "~/templates/preview.php",
	markupSet: [
      {name:'First Level Heading', key:"1", placeHolder:'Your title here...', closeWith:function(markItUp) { return miu.markdownTitle(markItUp, '=') } },
      {name:'Second Level Heading', key:"2", placeHolder:'Your title here...', closeWith:function(markItUp) { return miu.markdownTitle(markItUp, '-') } },
      {name:'Heading 3', key:"3", openWith:'### ', placeHolder:'Your title here...' },
      {name:'Heading 4', key:"4", openWith:'#### ', placeHolder:'Your title here...' },
      {name:'Heading 5', key:"5", openWith:'##### ', placeHolder:'Your title here...' },
      {name:'Heading 6', key:"6", openWith:'###### ', placeHolder:'Your title here...' },
      {separator:'---------------' },        
      {name:'Bold', key:"B", openWith:'**', closeWith:'**'},
      {name:'Italic', key:"I", openWith:'_', closeWith:'_'},
      {separator:'---------------' },
      {name:'Bulleted List', openWith:'- ' },
      {name:'Numeric List', openWith:function(markItUp) {
          return markItUp.line+'. ';
      }},
      {separator:'---------------' },
      {name:'Picture', key:"P", replaceWith:'![[![Alternative text]!]]([![Url:!:http://]!] "[![Title]!]")'},
      {name:'Link', key:"L", openWith:'[', closeWith:']([![Url:!:http://]!] "[![Title]!]")', placeHolder:'Your text to link here...' },
      {separator:'---------------'},    
      {name:'Quotes', openWith:'> '},
      {name:'Code Block / Code', openWith:'(!(\t|!|`)!)', closeWith:'(!(`)!)'},
      {separator:'---------------'},
      {name:'Preview', call:'preview', className:"preview"}
	]
}

// mIu nameSpace to avoid conflict.
miu = {
    markdownTitle: function(markItUp, chr) {
        heading = '';
        n = $.trim(markItUp.selection||markItUp.placeHolder).length;
        for(i = 0; i < n; i++) {
            heading += chr;
        }
        return '\n'+heading+'\n';
    }
}