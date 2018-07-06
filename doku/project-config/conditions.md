# Conditions

## Using conditions in task configuration

The execution of every task can skipped by condition:

```
'set project data:
  class: \Butler\Task\InputTask
  task: question
  options:
    projectname: 'What is the name of your Project?'
    projectvendor: 'What is the vendor name of your Project?'

'touch projectvendor',
  class: \Butler\Task\FilesystemTask
  task: touch
  condition: 'projectname != projectvendor'
  options:
    files:
      - '{projectvendor}-{projectname}.txt'
```
The task "touch projectvendor" is only executed if project config variables for "projectname" and "projectvendor" have NOT the same value.

Comparison Operators: (see: https://symfony.com/doc/current/components/expression_language/syntax.html#comparison-operators)

- ```==``` (equal)
- ```===``` (identical)
- ```!=``` (not equal)
- ```!==``` (not identical)
- ```<``` (less than)
- ```>``` (greater than)
- ```<=``` (less than or equal to)
- ```>=``` (greater than or equal to)
- ```matches``` (regex match)

Logical Operators: (see: https://symfony.com/doc/current/components/expression_language/syntax.html#logical-operators)

- ```not``` or ```!```
- ```and``` or ```&&```
- ```or``` or ```||```

Arithmetic Operators (see: https://symfony.com/doc/current/components/expression_language/syntax.html#arithmetic-operators)

- ```+``` (addition)
- ```-``` (subtraction)
- ```*``` (multiplication)
- ```/``` (division)
- ```%``` (modulus)
- ```**``` (pow)

Bitwise Operators (see: https://symfony.com/doc/current/components/expression_language/syntax.html#bitwise-operators)

- ```&``` (and)
- ```|``` (or)
- ```^``` (xor)

String Operators: (see: https://symfony.com/doc/current/components/expression_language/syntax.html#string-operators)

- ```~``` (concatenation)

Array Operators: (see: https://symfony.com/doc/current/components/expression_language/syntax.html#array-operators)

- ```in``` (contain)
- ```not in``` (does not contain)

Numeric Operators: (see: https://symfony.com/doc/current/components/expression_language/syntax.html#numeric-operators)

- ```..``` (range)

Ternary Operators: (see: https://symfony.com/doc/current/components/expression_language/syntax.html#ternary-operators)

- ```foo ? 'yes' : 'no'```
- ```foo ?: 'no' (equal to foo ? foo : 'no')```
- ```foo ? 'yes' (equal to foo ? 'yes' : '')```
