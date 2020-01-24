<?php

final class PhabricatorCzechTranslation
  extends PhutilTranslation {

  public function getLocaleCode() {
    return 'cs_CZ';
  }

  protected function getTranslations() {
    return array(
        'Reporting' => 'Reporting',
        'Module for reporting' => 'Reportovací modul',

        'Chart' => 'Graf',
        'User' => 'Uživatel',
        'Start' => 'Začátek',

        'End' => 'Konec',
        'Submit' => 'Potvrdit',
        'Employee Workload' => 'Vytíženost pracovníka',
        'Planned hours' => 'Naplánované hodiny',
        'Planned hours - tasks ending in period' => 'Naplánované hodiny (úkoly končí v intervalu)',
        'Open tasks' => 'Otevřené úkoly',
        'Open tests' => 'Otevřené úkoly pro testovaní',
        'Worktime tarif' => 'Pracovní úvazek',
        'Overload ratio' => 'Režijní činnost',
        'Total estimated hours in period' => 'V období má být odpracováno hodin',
        'Total estimated hours (from today)' => 'Zbývá odpracovat hodin (od dnes)',
        'Workload details' => 'Detaily o pracovním vytížení',
        'Tasks after completion date' => 'Úkoly, které již měly být ukončeny',
        'Tests after completion date' => 'Testy, které již měly být ukončeny',
        'Mon' => 'Po',
        'Tue' => 'Út',
        'Wed' => 'St',
        'Thu' => 'Čt',
        'Fri' => 'Pá',
        'Sat' => 'So',
        'Sun' => 'Ne',
        'The period for which workload is shown.' => 'Období, za které je vytíženost pracovníka zobrazena.',
        
        'Value between 0.0 - 1.0 (0.5 = half time - 4 hours a day, 1.0 = full time - 8 hours a day).It can be set by custom field: worktime-tarif. If it is not set, default value is 1.0 (8 hours a day).'
        => 'Hodnota v rozmezí 0.0 - 1.0 (0.5 = poloviční úvazek - 4 hodiny denně, 1.0 = plný úvazek - 8 hodin denně). Nastavuje se uživatelským polem: worktime-tarif. Pokud není nastaveno, vychozí hodnotou je 1.0 (8 hodin denně).',
        
        'Value between 0.0 - 1.0. Determines part of working time spended on meetings, business trips etc. It can be set by custom field: overload-ratio. If it is not set, default value is 0.2.'
        => 'Hodnota v rozmezí 0.0 - 1.0. Určuje, jakou část práce stráví pracovník na poradách, obchodních cestách apod. Nastavuje se pomocí uživatelského pole: overload-ratio. Pokud není nastaveno, výchozí hodnotou je 0.2.',
        
        'It is calculated as working days in period * worktime tarif * (1 - overload ratio).'
        => 'Počítá se jako: počet pracovních dnů ve vybraném období * pracovní úvazek * (1 - režijní činnost).',
        
        'It is calculated as working days from today to the end of period * worktime tarif * (1 - overload ratio).'
        => 'Počítá se jako: počet pracovních dnů ode dneška po konec zvoleného období * pracovní úvazek * (1 - režijní činnost).',
        
        'It is calculated as estimated time - actual implementation time. It includes tasks in sprint, progress or test with status open, test or waiting.'
        => 'Počítá se jako: naplánovaný čas - aktuálně strávený čas implementací. Jsou zde zahrnuty úkoly ve fázi: [Sprint, In Progress, Test] se stavem: [open, test, waiting].',
        
        'It is calculated as estimated time - actual implementation time. It includes tasks in sprint, progress or test with status open, test or waiting. Tasks ending in chosen interval.'
        => 'Počítá se jako: naplánovaný čas - aktuálně strávený čas implementací. Jsou zde zahrnuty úkoly ve fázi: [Sprint, In Progress, Test] se stavem: [open, test or waiting]. Úkoly končí ve vybraném období.',
        
        'It is calculated as estimated time - actual implementation time. It includes tasks in sprint or in progress with status open or waiting.'
        => 'Počítá se jako: naplánovaný čas - aktuálně strávený čas implementací. Jsou zde zahrnuty úkoly ve fázi: [Sprint, In Progress] se stavem: [open, waiting].',
        
        'It is calculated as estimated time - actual implementation time. It includes tasks in sprint or in progress with status open or waiting. Tasks ending in chosen interval.'
        => 'Počítá se jako: naplánovaný čas - aktuálně strávený čas implementací. Jsou zde zahrnuty úkoly ve fázi: [Sprint, In progress] se stavem: with status open or waiting. Tasks ending in chosen interval.',
        
        'It is calculated as estimated time testing - actual time testing. It includes tasks in test with status test.'
        => 'Počítá se jako: naplánovaný čas k testování - aktuálně strávený čas testováním. Jsou zde zahrnuty úkoly ve fázi: In test se stavem: test.',
        
        'It is calculated as estimated time testing - actual time testing. It includes tasks in test with status test. Tasks ending in chosen interval.'
        => 'Počítá se jako: naplánovaný čas k testování - aktuálně strávený čas testováním. Jsou zde zahrnuty úkoly ve fázi: In test se stavem: test. Úkoly končí ve vybraném období.',
        
        'List of tasks after estimated date which are in sprint or progress with status open or waiting'
        => 'Seznam úkolů, které již měly být ukončeny a jsou ve fázi:[Sprint, In Progress] a mají stav [open, waiting]',
        
        'List of tests after estimated date which are in test with status test'
        => 'Seznam úkolů, které již měly být ukončeny a jsou ve fázi: In test se stavem: test',


        'View workload of employees and manage it.' => 'Statistika o vytíženosti pracovníka',
        'Task Tree' => 'Strom úkolů',
        'Task tree' => 'Strom úkolů',
        'View tree of tasks with estimated and actual implementation time.' => 'Strom úkolů zobrazující naplánované a aktuálně odpracované hodiny na úkolu a jeho podúkolech.',
        'Home' => 'Domů',
        'Reporting module' => 'Reportovací modul',

        'Task' => 'Úkol',
        'Tasks' => 'Úkoly',
        'Open' => 'Otevřené',
        'Waiting' => 'Čekající',
        'Test' => 'Testování',
        'Resolved' => 'Vyřešené',
        'Total' => 'Celkem',
        'No tasks' => 'Žádné úkoly',
        'Status' => 'Stav',
        'Σ (hours)' => 'Σ (hodiny)',
        'Estimated hours on task by statuses' => 'Naplánované hodiny na úkolech podle stavů',
        'Task tree with estimated / actual hours of task and subtasks' => 'Strom úkolů s naplánovanými / odpracovanými hodinami na úkolu a jeho podúkolech',
        "Custom field: '%s' is probably not created" => "Uživatelské pole: '%s' pravděpodobně neexistuje",
        'Browse Task' => 'Výběr úkolu',
        'Type a task name...' => 'Zadej název úkolu...',
        'Close' => 'Zavřít',
        'Select' => 'Vybrat',
        'Direct Parent' => 'Přímý rodič',
        'Direct Subtask' => 'Přímý potomek',
        'Estimated' => 'Naplánováno',
        'Actual' => 'Odpracováno',
        'Estimated - testing' => 'Naplánováno - testování',
        'Actual - testing' => 'Odpracováno - testování',
        'No tasks found.' => 'Nebyly nalezeny žádné úkoly.',

        "Column: '%s' in your project's workboard for Reporting lib regularly working does not exist." =>
        "Ve workboardu projektu neexistuje sloupeček s názvem: '%s' potřebný pro správné fungování reportovacího modulu.",

    );
  }
}