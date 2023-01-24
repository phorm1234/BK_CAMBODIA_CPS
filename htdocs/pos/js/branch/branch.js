$(function(){	
	//-------------------------------------------------------------------------------------
    $('#urlgo').submit(function(){
    	$('#fixgrids').flexOptions({query:$('#url').val(),qtype:'name'}).flexReload();
        return false;
    });
    
    $('#searchgo').submit(function(){
    	$('#fixgrids').flexOptions({query:$('#search').val(),qtype:'brach_no'}).flexReload();
        return false;
    });
	
    $('#stop').click(function(){
    	$('#fixgrids').flexOptions({query:$('#url').val(),qtype:'name'}).flexReload();
        return false;
    });
    
});
//-------------------------------------------------------------------------------------
