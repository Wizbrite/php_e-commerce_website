<?php
$display = '0';
$previous = '';
$stored_op = '';
$stored_num = '';
$reset_next = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $current = isset($_POST['current']) ? $_POST['current'] : '0';
    $prev_display = isset($_POST['previous']) ? $_POST['previous'] : '';
    $stored_op = isset($_POST['stored_op']) ? $_POST['stored_op'] : '';
    $stored_num = isset($_POST['stored_num']) ? $_POST['stored_num'] : '';
    $reset_next = isset($_POST['reset_next']) ? $_POST['reset_next'] === '1' : false;
    
    $action = isset($_POST['action']) ? $_POST['action'] : '';
    $num = isset($_POST['num']) ? $_POST['num'] : '';
    $operator = isset($_POST['operator']) ? $_POST['operator'] : '';

    switch ($action) {
        case 'num':
            if ($reset_next || $current === '0' || $current === 'Error') {
                $display = $num;
                $reset_next = false;
            } else {
                $display = $current . $num;
            }
            $previous = $prev_display;
            break;

        case 'decimal':
            if ($reset_next || $current === 'Error') {
                $display = '0.';
                $reset_next = false;
            } elseif (strpos($current, '.') === false) {
                $display = $current . '.';
            } else {
                $display = $current;
            }
            $previous = $prev_display;
            break;

        case 'operator':
            if ($stored_op !== '' && !$reset_next) {
                // Perform previous calculation
                $result = calculate(floatval($stored_num), floatval($current), $stored_op);
                $display = strval($result);
                $previous = $result . ' ' . getSymbol($operator);
                $stored_num = strval($result);
            } else {
                $display = $current;
                $previous = $current . ' ' . getSymbol($operator);
                $stored_num = $current;
            }
            $stored_op = $operator;
            $reset_next = true;
            break;

        case 'equals':
            if ($stored_op !== '' && $stored_num !== '') {
                $result = calculate(floatval($stored_num), floatval($current), $stored_op);
                $display = strval($result);
                $previous = $stored_num . ' ' . getSymbol($stored_op) . ' ' . $current . ' =';
                $stored_op = '';
                $stored_num = '';
                $reset_next = true;
            } else {
                $display = $current;
                $previous = '';
            }
            break;

        case 'clear':
            $display = '0';
            $previous = '';
            $stored_op = '';
            $stored_num = '';
            $reset_next = false;
            break;

        case 'delete':
            if (!$reset_next && $current !== '0' && $current !== 'Error') {
                if (strlen($current) > 1) {
                    $display = substr($current, 0, -1);
                } else {
                    $display = '0';
                }
            } else {
                $display = '0';
            }
            $previous = $prev_display;
            break;
    }
}

function calculate($n1, $n2, $op) {
    switch ($op) {
        case '+': return $n1 + $n2;
        case '-': return $n1 - $n2;
        case '*': return $n1 * $n2;
        case '/': return $n2 == 0 ? 'Error' : $n1 / $n2;
        case '%': return $n2 == 0 ? 'Error' : fmod($n1, $n2);
        default: return $n2;
    }
}

function getSymbol($op) {
    $symbols = ['+' => '+', '-' => '−', '*' => '×', '/' => '÷', '%' => '%'];
    return $symbols[$op] ?? $op;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My PHP Calculator</title>
    <link rel="stylesheet" href="col.css">
</head>
<body>
    <div class="particles" id="particles"></div>

    <div class="calculator-container">
        <div class="calculator-header">Premium Calculator</div>
        
        <div class="display">
            <div class="previous-operation"><?php echo htmlspecialchars($previous); ?></div>
            <div class="current-input"><?php echo htmlspecialchars($display); ?></div>
        </div>

        <form method="POST" id="calcForm">
            <input type="hidden" name="current" value="<?php echo htmlspecialchars($display); ?>">
            <input type="hidden" name="previous" value="<?php echo htmlspecialchars($previous); ?>">
            <input type="hidden" name="stored_op" value="<?php echo htmlspecialchars($stored_op); ?>">
            <input type="hidden" name="stored_num" value="<?php echo htmlspecialchars($stored_num); ?>">
            <input type="hidden" name="reset_next" value="<?php echo $reset_next ? '1' : '0'; ?>">
            <input type="hidden" name="action" id="action">
            <input type="hidden" name="num" id="num">
            <input type="hidden" name="operator" id="operator">

            <div class="buttons-grid">
                <button type="button" class="btn-function" onclick="submitAction('clear')">AC</button>
                <button type="button" class="btn-function" onclick="submitAction('delete')">⌫</button>
                <button type="button" class="btn-function" onclick="submitOperator('%')">%</button>
                <button type="button" class="btn-operator" onclick="submitOperator('/')">÷</button>

                <button type="button" class="btn-number" onclick="submitNumber('7')">7</button>
                <button type="button" class="btn-number" onclick="submitNumber('8')">8</button>
                <button type="button" class="btn-number" onclick="submitNumber('9')">9</button>
                <button type="button" class="btn-operator" onclick="submitOperator('*')">×</button>

                <button type="button" class="btn-number" onclick="submitNumber('4')">4</button>
                <button type="button" class="btn-number" onclick="submitNumber('5')">5</button>
                <button type="button" class="btn-number" onclick="submitNumber('6')">6</button>
                <button type="button" class="btn-operator" onclick="submitOperator('-')">−</button>

                <button type="button" class="btn-number" onclick="submitNumber('1')">1</button>
                <button type="button" class="btn-number" onclick="submitNumber('2')">2</button>
                <button type="button" class="btn-number" onclick="submitNumber('3')">3</button>
                <button type="button" class="btn-operator" onclick="submitOperator('+')">+</button>

                <button type="button" class="btn-number btn-zero" onclick="submitNumber('0')">0</button>
                <button type="button" class="btn-number" onclick="submitAction('decimal')">.</button>
                <button type="button" class="btn-equals" onclick="submitAction('equals')">=</button>
            </div>
        </form>
    </div>

    <script>
        
        const particlesContainer = document.getElementById('particles');
        for (let i = 0; i < 50; i++) {
            const particle = document.createElement('div');
            particle.className = 'particle';
            particle.style.left = Math.random() * 100 + '%';
            particle.style.animationDelay = Math.random() * 15 + 's';
            particle.style.animationDuration = (10 + Math.random() * 10) + 's';
            particlesContainer.appendChild(particle);
        }

        
        function submitNumber(n) {
            document.getElementById('action').value = 'num';
            document.getElementById('num').value = n;
            document.getElementById('calcForm').submit();
        }

        function submitOperator(op) {
            document.getElementById('action').value = 'operator';
            document.getElementById('operator').value = op;
            document.getElementById('calcForm').submit();
        }

        function submitAction(act) {
            document.getElementById('action').value = act;
            document.getElementById('calcForm').submit();
        }
    </script>
</body>
</html>