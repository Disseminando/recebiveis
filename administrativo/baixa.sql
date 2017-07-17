-- phpMyAdmin SQL Dump
-- version 4.0.4
-- http://www.phpmyadmin.net
--
-- Máquina: localhost
-- Data de Criação: 22-Jun-2017 às 19:16
-- Versão do servidor: 5.6.12-log
-- versão do PHP: 5.4.16

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Base de Dados: `recebiveis`
--

-- --------------------------------------------------------

--
-- Estrutura da tabela `baixa`
--

CREATE TABLE IF NOT EXISTS `baixa` (
  `id_bax` int(11) NOT NULL AUTO_INCREMENT,
  `data_bax` date NOT NULL,
  `clin_id` int(4) NOT NULL,
  `med_id` int(4) NOT NULL,
  `valor_lanc_id` int(4) NOT NULL,
  `cadastrador_bax` varchar(40) NOT NULL,
  PRIMARY KEY (`id_bax`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
