window.addEventListener('load', function() {
    
    // --- 部分 1: 高亮功能 ---
    var highlightElements = document.querySelectorAll('.hilightable');

    // 也可以定义一个函数来处理切换，这样代码更整洁
    function toggleHighlight() {
        this.classList.toggle('highlight');
    }

    for (var i = 0; i < highlightElements.length; i++) {
        highlightElements[i].addEventListener('focus', toggleHighlight);
        highlightElements[i].addEventListener('blur', toggleHighlight);
    }

    // --- 部分 2: 表单验证 ---
    var form = document.getElementById('mainForm');
    var requiredElements = document.querySelectorAll('.required');

    // 处理表单提交
    form.addEventListener('submit', function(e) {
        for (var i = 0; i < requiredElements.length; i++) {
            // 检查是否为空
            if (requiredElements[i].value.trim() === "") {
                e.preventDefault(); // 阻止提交
                requiredElements[i].classList.add('error'); // 添加错误红框
            }
        }
    });

    // 处理输入时的错误清除
    for (var i = 0; i < requiredElements.length; i++) {
        requiredElements[i].addEventListener('input', function() {
            // 只要用户开始输入，就认为他们在修正错误，移除红框
            this.classList.remove('error');
        });
    }

});