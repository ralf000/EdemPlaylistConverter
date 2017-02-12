<?php

require __DIR__ . '/autoload.php';

require_once __DIR__ . '/bootstrap/app.php';

/**
 * Проблема: стандартный плейлист edem не отсортирован
 * имеются лишние категории каналов
 * каналы не в тех категориях
 * лишние каналы
 * названия некоторых каналов не позволяют задействовать телепрограмму
 * нет возможности добавить свои каналы
 */

/**
 * Решение: написать программу которая будет сортировать каналы
 * удалять лишние категории каналов и создавать свои
 * переносить каналы в категории
 * удалять лишние каналы
 * переименовывать каналы
 * добавлять свои каналы
 */

/**
 * Механизм решения:
 * Классы:
 * abstract class File
 * может удалить файл, создать файл, переименовать файл, прочитать файл
 * хранит дескриптор соединения,
 * class Playlist extends File
 * содержит каналы,
 * может сортировать каналы
 * добавлять новые каналы, удалять каналы
 */